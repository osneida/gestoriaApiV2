<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiftControl;
use App\Models\Worker;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \PDF;


class ShiftControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itemsPerPage = (int) request('itemsPerPage');
        $sc = ShiftControl::filtered();
        return response()->json(["success" => true, "data" => $sc->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }
    public function indexByDay()
    {
        $scs = ShiftControl::filtered()->get();


        return response()->json(["success" => true, "data" => self::processShiftControls($scs)]);
    }

    private static function processShiftControls($scs)
    {
        $result = [];
        foreach ($scs as $sc) {
            $result[$sc->date][] = $sc;
        }
        return $result;
    }


    public function store()
    {
        //
        $last = ShiftControl::where("date", request("date"))
            ->where("contract_id", request("contract_id"))
            ->where("hour", "<=", request("hour"))
            ->orderBy("hour", "desc")
            ->first();
        if ($last) {

            if ($last->hour == request("hour"))
                return ["success" => false, "error" => "No pots introduir la mateixa hora 2 vegades en el mateix dia"];
            if ($last->action == request("action"))
                return ["success" => false, "error" => "No pots repetir la mateixa accio 2 vegades seguides"];
        } else if (request("action") !== 'start') {
            return ["success" => false, "error" => "La primera accio del dia ha de ser entrar"];
        }
        //return;
        ShiftControl::create(request()->all());
        return ["success" => true];
    }

    public function show($id)
    {
        //
        return ShiftControl::find($id);
    }

    public function update($id)
    {
        Log::info('mensaje 1',['data']);
        $last = ShiftControl::where("date", request("date"))
        ->where("contract_id", request("contract_id"))
        ->where("hour", "<=", request("hour"))
        ->orderBy("hour", "desc")
        ->first();
        Log::info('mensaje 2',['data'=> $last->action]);
        if ($last){
            Log::info('mensaje 3',['data' => request("action")]);
            if ($last->action !== request("action")) {
                Log::info('mensaje 4',['data']);
                return ["success" => false, "error" => "No puedes repetir la misma acción dos veces seguidas"];
                Log::info('mensaje 5',['data']);
            } else {
                Log::info('mensaje 6',['data']);
                ShiftControl::where("id", $id)->update(request()->all());
                Log::info('mensaje 7',['data']);
                return ["success" => true];
                Log::info('mensaje 8',['data']);
            }
        }
    }

    public function download()
    {
        $res = $this->indexByDay();

        $data = $res->getOriginalContent()["data"];

        $start = strtotime(request("from"));
        $end = strtotime(request("to"));
        $dayOfWeek = date('w', $start);
        $weeks = [];

        $week = [];

        for ($i = 1; $i < $dayOfWeek; $i++) {
            $week[] = [];
        }

        $i = 0;
        $max = 0;
        $start_h = null;
        $end_h = null;
        $dif = 0;
        $total = 0;

        while ($start <= $end) {
            $sc = $data[date('Y-m-d', $start)] ?? [];

            foreach ($sc as $s) {
                $m = new DateTime($s['date'] . ' ' . $s['hour'] . ':00');

                if ($s["action"] === 'start') $start_h = $m;
                else $end_h = $m;
                if ($end_h !== null && $start_h !== null) {
                    $intervalo = $start_h->diff($end_h);
                    $dif += (int) $intervalo->format('%H') + ((int)$intervalo->format('%i') / 60);
                    $start_h = null;
                    $end_h = null;
                }
            }

            $week[] = [
                "date" => date('Y-m-d', $start),
                "sc" => $sc,
                "dif" => $dif
            ];

            $total += $dif;
            $dif = 0;
            if (count($sc) > $max) {
                $max = count($sc);
            }
            $start = strtotime(date('Y-m-d', $start) . " +1 days");




            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
        }
        $w = Worker::find(request("worker_id"));

        $weeks[] = $week;
        $info = ['month' => date('m-Y', strtotime(request("from"))), "worker_name" => $w->full_name_with_dni, "max" => $max, "total" => $total];
        $pdf = PDF::loadView('pdf.shift_control', compact("weeks"), compact("info"));
        $pdf->save(public_path("shift_control.pdf"));

        return
            base64_encode($pdf->output());
    }


    public function downloadGeneral()
    {
        $ws = Worker::whereHas("companies", function ($q) {
            $q->where("id", request("company_id"));
        })->get();

        $start = strtotime(request("from"));
        $end = strtotime(request("to"));
        $dayOfWeek = date('w', $start);
        $week_start = [];

        for ($i = 1; $i < $dayOfWeek; $i++) {
            $week_start[] = [];
        }
        $infos = [];
        foreach ($ws as $w) {

            $data = ShiftControl
                ::whereDate("date", ">=", request("from"))
                ->whereDate("date", "<=", request("to"))
                ->whereHas("contract", function ($q) use ($w) {
                    $q->where("worker_id", $w->id);
                })->get();
            $data = self::processShiftControls($data);

            $i = 0;
            $max = 0;
            $start_h = null;
            $end_h = null;
            $dif = 0;
            $total = 0;
            $week = $week_start;
            $weeks = [];
            $start = strtotime(request("from"));


            while ($start <= $end) {
                $sc = $data[date('Y-m-d', $start)] ?? [];

                foreach ($sc as $s) {
                    $m = new DateTime($s['date'] . ' ' . $s['hour'] . ':00');

                    if ($s["action"] === 'start') $start_h = $m;
                    else $end_h = $m;
                    if ($end_h !== null && $start_h !== null) {
                        $intervalo = $start_h->diff($end_h);
                        $dif += (int) $intervalo->format('%H') + ((int)$intervalo->format('%i') / 60);
                        $start_h = null;
                        $end_h = null;
                    }
                }

                $week[] = [
                    "date" => date('Y-m-d', $start),
                    "sc" => $sc,
                    "dif" => $dif
                ];

                $total += $dif;
                $dif = 0;
                if (count($sc) > $max) {
                    $max = count($sc);
                }
                $start = strtotime(date('Y-m-d', $start) . " +1 days");




                if (count($week) === 7) {
                    $weeks[] = $week;
                    $week = [];
                }
            }

            $weeks[] = $week;
            $infos[] = [
                'month' => date('m-Y', strtotime(request("from"))),
                "worker_name" => $w->full_name_with_dni,
                "max" => $max,
                "total" => $total,
                "weeks" => $weeks
            ];
        }

        //return $infos;
        $pdf = PDF::loadView('pdf.shift_control_global', compact("infos"));
        $pdf->save(public_path("shift_control.pdf"));

        return
            base64_encode($pdf->output());
    }


    public function destroy($id)
    {
        //
    }
}
