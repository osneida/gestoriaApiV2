<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Holidays;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class HolidaysController extends Controller
{
    public function index()
    {
        $itemsPerPage = request('itemsPerPage');
        $holidays = Holidays::filtered();
        return response()->json($holidays->paginate($itemsPerPage ?? 10));
    }

    public function getForAgency()
    {
        $itemsPerPage = request('itemsPerPage');
        $date = date('Y-m-d');
        $yearStart = date('Y-01-01');
        $search = request()->input('search');

        $holidays = Holidays::where('approved', 1)
                ->where('approved_end_date',  '>=', $yearStart)
                ->where('approved_end_date',  '<=', $date);
        
        if ($search !== null && $search !== '') {
            $holidays->where(function ($query) use ($search) {
                $query->whereHas('contract.worker', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('dni', 'LIKE', '%' . $search . '%');
                })->orWhereHas('contract.company', function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }
                
        $holidays = $holidays->groupBy('contract_id')
                    ->select('contract_id', DB::raw('SUM(spended_days) as total_spended_days'))
                    ->with(['contract.worker','contract.agreement'])
                    ->paginate($itemsPerPage);

        return response()->json($holidays);
    }

    

    public function calendar()
    {
        $holidays = Holidays::filtered()->get();

        $event = [];
        foreach ($holidays as $holiday) {
            $event[] = [
                "id" => $holiday->id,
                "name" => $holiday->contract->worker->full_name_with_dni,
                "start" => $holiday->approved ? $holiday->approved_start_date : $holiday->requested_start_date,
                "end" => $holiday->approved ? $holiday->approved_end_date : $holiday->requested_end_date,
                "approved" => $holiday->approved
            ];
        }
        return $event;
    }


    public function store()
    {
        $startDate = request("start_date");
        $endDate = request("end_date");
        $contract_id = request("contract");

        $contract = Contract::where('id', $contract_id)->with(["holidays" => function ($query) use (&$startDate) {
            $query->whereYear('requested_start_date', date("Y", strtotime($startDate)));
        }])->first();
        // Log::info('en el store',['data'=>$contract->holidays_type]);
        // dd("contract type");
        $daysDiff = self::contractDateDifference($startDate, $endDate, $contract->holidays_type, $contract->holidays_location);
        Log::info('en el store',['data'=>$daysDiff]);
        $daysUsed = $daysDiff;
        foreach ($contract->holidays as $holidays) {
            $daysUsed += $holidays->spended_days;
        }
        $remainingDays = self::calculateHolidays($contract->worker_id, $contract->company_id, date("Y", strtotime($startDate))) - $daysUsed;
        if ($daysUsed == 0) {
            return response()->json(["success" => false, "msg" => "No has seleccionado ningún día. Elige otro período que tenga por lo menos 1 día hábil."]);
        }
        if ($remainingDays < 0) {
            $remainingDays *= -1;
            return response()->json(["success" => false, "msg" => "Has superado el límite de días de vacaciones de tu contrato este año por $remainingDays días."]);
        }

        $hs = Holidays::whereHas("contract", function ($q) use ($contract) {
            $q->where("worker_id", $contract->worker_id);
        })->where(function ($q) use ($startDate, $endDate) {
            $q->where(function ($q) use ($startDate, $endDate) { //Si esta aprovada agafem dates finals
                $q->where("approved", TRUE);
                $q->whereDate("approved_start_date", "<=", $endDate);
                $q->whereDate("approved_end_date", ">=", $startDate);
            });

            $q->orWhere(function ($q) use ($startDate, $endDate) { //Si no esta aprovada agafem dates request
                $q->whereNull("approved");

                $q->whereDate("requested_start_date", "<=", $endDate);
                $q->whereDate("requested_end_date", ">=", $startDate);
            });
        })->exists();
        if ($hs) {
            return response()->json(["success" => false, "msg" => "Aquestes vacances coincideixen amb unes altres que ja tens demanades o aprovades"]);
        }

        Holidays::create([
            "contract_id" => $contract_id,
            "requested_start_date" => $startDate,
            "requested_end_date" => $endDate,
            "spended_days" => $daysDiff
        ]);

        return response()->json(["success" => true]);
    }


    public function storeAll()
    {
        DB::beginTransaction();
        $startDate = request("start_date");
        $endDate = request("end_date");
        $user = JWTAuth::parseToken()->authenticate();

        $companyIds = $user->companies()->where('role', 'gestor')->pluck('id')->toArray();
        Log::info('mensaje idssssss',['data'=>$companyIds]);
        $contracts = Contract::whereIn('company_id', $companyIds)->where(function ($q) use ($endDate) {
            $q->whereDate("contract_end_date", ">=", $endDate)->orWhereNull("contract_end_date");
        })
            ->with(["worker", "holidays" => function ($query) use (&$startDate) {
                $query->whereYear('requested_start_date', date("Y", strtotime($startDate)));
            }])->get();

        $e = [];
        foreach ($contracts as $contract) {
            $daysDiff = self::contractDateDifference($startDate, $endDate, $contract->holidays_type, $contract->holidays_location);

            $daysUsed = $daysDiff;
            if ($daysUsed == 0) {
                return response()->json(["success" => false, "msg" => "No has seleccionado ningún día. Elige otro período que tenga por lo menos 1 día hábil."]);
            }
            foreach ($contract->holidays as $holidays) {
                $daysUsed += $holidays->spended_days;
            }
            $remainingDays = $contract->days_of_holidays - $daysUsed;

            if ($remainingDays < 0) {
                $remainingDays *= -1;
                return response()->json(["success" => false, "msg" => $contract->worker->full_name_with_dni . " ha superado el límite de días de vacaciones de su contrato este año por $remainingDays días."]);
            }

            Holidays::create([
                "contract_id" => $contract->id,
                "requested_start_date" => $startDate,
                "requested_end_date" => $endDate,
                "spended_days" => $daysDiff,
                "approved_start_date" => $startDate,
                "approved_end_date" => $endDate,
                "approved" => true,
                "approver_id" => $user->id,
                "approval_date" => now()
            ]);
        }
        DB::commit();

        return response()->json(["success" => true, "errors" => $e]);
    }

    public function update($id)
    {
        $startDate = request("start_date");
        $endDate = request("end_date");
        $approved = request('approved');

        $holidays = Holidays::where('id', $id)->with(['contract'])->first();
        $holidays->spended_days = 0;
        if ($approved) {
            $holidays->spended_days = self::contractDateDifference($startDate, $endDate, $holidays->contract->holidays_type, $holidays->contract->holidays_location);
            $holidays->approved_start_date = $startDate;
            $holidays->approved_end_date = $endDate;
        }
        $holidays->approved = $approved;
        $holidays->approver_id = auth()->user()->id;
        $holidays->approval_date = now();
        $holidays->notificated_at = null;
        $holidays->notification_attempts = 0;
        $holidays->save();

        return response()->json(["success" => true]);
    }

    public static function contractDateDifference($startDate, $endDate, $holidays_type, $holidays_location)
    {
        Log::info('mensaje final location',['data'=>$holidays_location]);
        if ($holidays_type == 'n') {
            Log::info('mensaje pasa por la n',['data']);
            return date_diff(date_create($endDate), date_create($startDate))->format("%a") + 1;
        }
        if ($holidays_type == 'h') {
            Log::info('mensaje pasa por la h',['data']);
            if($holidays_location == 'Madrid'){
                $daysDiff = self::diffMadrid($startDate, $endDate, $holidays_type, $holidays_location);
            }else{
                $daysDiff = self::diffCatalunya($startDate, $endDate, $holidays_type, $holidays_location);
            }

            Log::info('mensaje final',['data']);
            // dd("hasta aca llego");
            return $daysDiff;
        }
    }

    public static function diffCatalunya($startDate, $endDate, $holidays_type, $holidays_location)
    {
        Log::info('mensaje pasa por Catalunya',['data']);
              
        $year = date("Y", strtotime($startDate));
        $festivos = @file_get_contents('https://analisi.transparenciacatalunya.cat/resource/yf2b-mjr6.json?$limit=30000&$where=any_calendari%20=%20' . $year);
        $festivos = json_decode($festivos, true);
        $festivosLocales = @file_get_contents('https://analisi.transparenciacatalunya.cat/resource/b4eh-r8up.json?$limit=30000&$where=any_calendari%20=%20' . $year . '&ajuntament_o_nucli_municipal=' . urlencode($holidays_location));
        $festivosLocales = json_decode($festivosLocales, true);
        Log::info('festivos de catalunya',['data' => $festivos]);

        $festivos = array_merge($festivos, $festivosLocales);
        $festivos = array_column($festivos, "data");
        $festivos = array_map(function ($d) {
            return date('Y-m-d', strtotime($d));
        }, $festivos);

        $daysDiff = date_diff(date_create($endDate), date_create($startDate))->format("%a") + 1;
        $startDate_ = date('Y-m-d', strtotime($startDate));
        $endDate_ = date('Y-m-d', strtotime($endDate));
        foreach ($festivos as $festivo) {
            if ($startDate_ <= $festivo && $festivo <= $endDate_) {
                $daysDiff--;
                //var_dump("festivo", $festivo);
            }
        }
        $startDate_ = date_create($startDate);
        $endDate_ = date_create($endDate)->modify('+1 day');
        $period = new DatePeriod($startDate_, DateInterval::createFromDateString('1 day'), $endDate_);
        foreach ($period as $dt) {
            if (($dt->format('N') == 6 || $dt->format('N') == 7) && !in_array($dt->format('Y-m-d'), $festivos)) {
                $daysDiff--;
                //var_dump("sabado o domingo", $dt->format('Y-m-d'));
            }
        }

        Log::info('mensaje final antes de retornar BARCELONA',['data' => $daysDiff]);
        return $daysDiff;
    }

    public static function diffMadrid($startDate, $endDate, $holidays_type, $holidays_location)
    {
        Log::info('mensaje pasa por Madrid',['data']);

        $year = date("Y", strtotime($startDate));
        $url = 'https://datos.comunidad.madrid/catalogo/dataset/2f422c9b-47df-407f-902d-4a2f44dd435e/resource/453162e0-bd61-4f52-8699-7ed5f33168f6/download/festivos_regionales.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $festivosMadrid = json_decode($response, true);
        $festivosMadrid = array_column($festivosMadrid['data'], "fecha_festivo");
        Log::info('festivos de Madrid',['data' => $festivosMadrid]);
        $festivosMadrid = array_map(function($fecha) {
            return date("Y-m-d", strtotime(str_replace('/', '-', $fecha)));
        }, $festivosMadrid);
        
        $daysDiff = date_diff(date_create($endDate), date_create($startDate))->format("%a") + 1;
        $startDate_ = date('Y-m-d', strtotime($startDate));
        $endDate_ = date('Y-m-d', strtotime($endDate));
        
        foreach ($festivosMadrid as $festivo) {
            if ($startDate_ <= $festivo && $festivo <= $endDate_) {
                $daysDiff--;
                // var_dump("festivo", $festivo);
            }
        }
        
        $startDate_ = date_create($startDate);
        $endDate_ = date_create($endDate)->modify('+1 day');
        $period = new DatePeriod($startDate_, DateInterval::createFromDateString('1 day'), $endDate_);
        
        foreach ($period as $dt) {
            if (($dt->format('N') == 6 || $dt->format('N') == 7) && !in_array($dt->format('Y-m-d'), $festivosMadrid)) {
                $daysDiff--;
                // var_dump("sabado o domingo", $dt->format('Y-m-d'));
            }
        }
        Log::info('mensaje final antes de retornar MADRID',['data' => $daysDiff]);
        return $daysDiff;
    }

    public function anulate($id)
    {
        $holidays = Holidays::where('id', $id)->with(['contract'])->first();
        $holidays->spended_days = 0;
        $holidays->approved_start_date = null;
        $holidays->approved_end_date = null;
        $holidays->approved = FALSE;

        $holidays->notificated_at = null;
        $holidays->notification_attempts = 0;
        $holidays->save();

        return response()->json(["success" => true]);
    }

    public static function calculateHolidays($w, $c, $y)
    {
        //Log::debug("$w $c $y");
        $contract = Contract::where("company_id", $c)->where("worker_id", $w)->orderBy("created_at", "ASC")->first();
        if ($contract) {

            if (explode("-", $contract->contract_start_date)[0] === $y) {
                //TODO calculo

                $datetime1 = date_create($contract->contract_start_date);
                $datetime2 = date_create("$y-12-31");

                $interval = date_diff($datetime1, $datetime2);

                $days = $interval->format("%a");
                $months =  $days / 30;
                return round($contract->days_of_holidays * $months / 12);
            }
            return $contract->days_of_holidays;
        }
    }

    public static function spendedHolidays($w, $c, $y)
    {
        return Holidays::whereHas("contract", function ($q) use ($w, $c) {
            $q->where("company_id", $c);
            $q->where("worker_id", $w);
        })->whereYear("approved_start_date", $y)
            ->sum("spended_days");
    }
    public function getMyCurrentHolidays()
    {

        return [
            "has" => self::calculateHolidays(auth()->user()->worker_id, auth()->user()->company_id, now()->year),
            "spended" => self::spendedHolidays(auth()->user()->worker_id, auth()->user()->company_id, now()->year)
        ];
    }
}
