<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\Certificates;
use App\Mail\MyWorkerUpdate;
use App\Mail\Payrolls;
use App\Mail\PayrollsForWorker;
use App\Models\Agreement;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Commission;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkCenter;
use App\Models\Salary;
use App\Models\WorkerReplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class WorkerController extends Controller
{
    public function filtersData()
    {
        if (request()->wantsJson()) {
            $companies = Company::select('id', 'name', "active")->whereNotNull('name')->orderBy('name')->get();
            return response()->json(["success" => true, 'companies' => $companies]);
        }
    }

    public function paginatedForReport()
    {
        $itemsPerPage = (int) request('itemsPerPage');
        $workers = Worker::filtered();
        return response()->json(["success" => true, "data" => $workers->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }

    public function allFiltered()
    {
        $workers = Worker::filtered();
        return response()->json(["success" => true, "data" => $workers->get()]);
    }
    public function allFilteredVacation($id)
    {
        if (request()->wantsJson()) {

            $workers = Worker::whereHas('companies', function ($q) use ($id) {
                $q->where("id", $id);
            })->with(["latestContract"])->where("archive", false);
            return response()->json(["success" => true, "data" => $workers->get()]);
        }
    }

    public function allFilteredWorkingDay()
    {
        if (request()->wantsJson()) {

            $workers = Worker::filtered();
            return response()->json(["success" => true, "data" => $workers->get()]);
        }
    }

    public function show($id)
    {
        if (request()->wantsJson()) {
            $worker = Worker::with(["companies" => function ($q) {
                $q->select("id", "name", "holiday_responsible")->with("responsible");
            }, "latestContract" => function ($query) {
                $query
                    //->whereDate("contract_start_date", "<=", now())
                    ->with(["agreement", "category", "company", "file"]);
            }, "responsible"])->select("id", "first_name", "last_name", "dni", "email", "holiday_responsible")->find($id);

            $user = User::whereIn('role_id', [1, 2])->where('company_id', $worker->companies[0]->id)->first();

            return response()->json(["success" => true, "worker" => $worker, "user" => $user]);
        }
    }

    public function store()
    {
        if (request()->wantsJson()) {
//            Log::info('mensaje rubenurrieta',['data'=> request()]);
            try {
                DB::beginTransaction();
                $rules = [
                    "first_name" => 'required|min:2',
                    "last_name" => 'required|min:2',
                    "email" => '',
                    "dni" => 'required', //? |unique:workers,dni,NULL,id,deleted_at,NULL',
                    "company_id" => 'required|exists:companies,id',
                ];
                $validator = Validator::make(request()->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 422);
                }

                $existDni = Worker::where('dni', request("dni"))->exists();

                $worker = Worker::updateOrCreate([
                    "dni" => request("dni"),
                ], [
                    "first_name" => request("first_name"),
                    "last_name" => request("last_name"),
                    "document_type" => "dni",
                    "email" => request("email")
                ]);
                
                $worker->existWorker = $existDni;
              
                DB::table("company_worker")->updateOrInsert([
                    "company_id" => request("company_id"),
                    "worker_id" => $worker->id
                ], [
                    "company_id" => request("company_id"),
                    "worker_id" => $worker->id
                ]);
               if(request("worker_id_replace")){
                    DB::table("worker_replaces")->updateOrInsert([
                        "worker_id_replace" => request("worker_id_replace"), //a quien remplaza
                        "company_id"        => request("company_id"),
                        "worker_id"         => $worker->id
                    ]);
                }
                DB::commit();
                return response()->json(["success" => true, "data" => $worker]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }

    public function update(int $id)
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $worker = Worker::find($id);
                if (!$worker) {
                    return response()->json(["success" => false], 404);
                }
                $rules = [
                    "first_name" => 'required|min:2',
                    "last_name" => 'required|min:2',
                    "email" => 'required|email',
                    "dni" => 'required|unique:workers,dni,' . $id . ',id,deleted_at,NULL',
                    "company_id" => 'required|exists:companies,id',
                ];
                $validator = Validator::make(request()->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 422);
                }

                if ($worker) {
                    $worker->dni = request("dni");
                    $worker->first_name = request("first_name");
                    $worker->last_name = request("last_name");
                    $worker->document_type = "dni";
                    $worker->email = request("email");
                    $worker->save();
                    $workerCompany = DB::table("company_worker")->where("worker_id", $id)->latest("inserted_at");
                    $workerCompany->update([
                        "company_id" => request("company_id")
                    ]);
                    DB::commit();
                    return response()->json(["success" => true]);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }

    public function updateMyWorker()
    {
        $w = Worker::where("id", JWTAuth::parseToken()->authenticate()->worker_id)->with(["latestContract"])->first();
        switch (request("camp")) {
            case "iban":

                Contract::where("id", $w->latestContract[0]->id)->update([request("camp") => request("value")]);
                break;
            case "email":
                $w->email = request("value");
                User::where("worker_id", $w->id)->update(["email" => request("value")]);
                break;
            case "address":
                Contract::where("id", $w->latestContract[0]->id)->update(request("value"));
        }
        $w->save();
        $email = app()->environment('production') ? env("NOTIFICATION_MAIL") : env("DEVELOPER_MAIL");

        Mail::to($email)->send(new MyWorkerUpdate($w, request("camp"), request("value")));
        return ["success" => true];
    }

    public function dataForSendPayrolls($workerId)
    {
        if (request()->wantsJson()) {
            $worker = Worker::select("id", "first_name", "last_name", "dni")->find($workerId);
            $payrolls = Payroll::select("id", "period", "processed")->whereWorkerId($workerId)->orderBy('period', 'desc')->get();
            $certificates = Certificate::select("id", "period", "processed")->whereWorkerId($workerId)->orderBy('period', 'desc')->get();
            return response()->json([
                "success" => true,
                'worker' => $worker,
                'payrolls' => $payrolls,
                'certificates' => $certificates
            ]);
        }
        abort(401);
    }

    /**
     * @param $workerId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendPayrollsByEmail($workerId)
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $worker = Worker::find($workerId);
                if ($worker->exists) {
                    $inputPayrolls = \request("payrolls");
                    $payrolls = Payroll::select("id", "period", "document_file", "processed")
                        ->whereWorkerId($workerId)
                        ->whereIn("id", $inputPayrolls);

                    $payrollsQuery = with($payrolls)->get();
                    if ($payrollsQuery) {
                        $data = [];
                        foreach ($payrollsQuery as $payroll) {
                            $data[] = [
                                "s3_path" => $payroll->document_file,
                                "period" => sprintf('nòmina-%s.pdf', $payroll->period)
                            ];
                        }

                        $subject = request("subject");
                        $message = request("message");
                        $email = app()->environment('production') ? $worker->email : env("DEVELOPER_MAIL");
                        Mail::to($email)->locale('es')->send(
                            new Payrolls($subject, $message, $data, true)
                        );

                        if (Mail::failures()) {
                            return response()->json(["success" => false]);
                        }

                        $payrollsUpdate = with($payrolls);
                        $payrollsUpdate->update([
                            "processed" => now()
                        ]);

                        DB::commit();
                        return response()->json(["success" => true]);
                    }
                }
                return response()->json(["success" => false]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "error" => $exception->getMessage()]);
            }
        }
        abort(401);
    }
    public function sendCertificateByEmail($workerId)
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $worker = Worker::find($workerId);
                if ($worker->exists) {
                    $inputPayrolls = \request("payrolls");
                    $payrolls = Certificate::select("id", "period", "document_file", "processed")
                        ->whereWorkerId($workerId)
                        ->whereIn("id", $inputPayrolls);

                    $payrollsQuery = with($payrolls)->get();
                    if ($payrollsQuery) {
                        $data = [];
                        foreach ($payrollsQuery as $payroll) {
                            $data[] = [
                                "s3_path" => $payroll->document_file,
                                "period" => sprintf('certificat-%s.pdf', $payroll->period)
                            ];
                        }

                        $subject = request("subject");
                        $message = request("message");
                        $email = app()->environment('production') ? $worker->email : env("DEVELOPER_MAIL");
                        Mail::to($email)->locale('es')->send(
                            new Certificates($subject, $message, $data, true)
                        );

                        if (Mail::failures()) {
                            return response()->json(["success" => false]);
                        }

                        $payrollsUpdate = with($payrolls);
                        $payrollsUpdate->update([
                            "processed" => now()
                        ]);

                        DB::commit();
                        return response()->json(["success" => true]);
                    }
                }
                return response()->json(["success" => false]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "error" => $exception->getMessage()]);
            }
        }
        abort(401);
    }

    public function destroy(int $id, int $user_id)
    {
        if (request()->wantsJson()) {
            $user = User::find($user_id);
            if (!$user) {
                return response()->json(["message" => "user not found"]);
            }
            if ($user->role_id === User::COMPANY_ROLE) {
                $company = Company::whereUserId($user_id)->first();
                if (!$company) {
                    return response()->json(["message" => "company not found"]);
                }
                $exitsWorkerRelation = DB::table("company_worker")->where("company_id", $company->id)->where("worker_id", $id)->first();
                if (!$exitsWorkerRelation) {
                    return response()->json(["message" => "company worker relation not found"]);
                }
            }

            if ($user->role_id === User::WORKER_ROLE) {
                return response()->json(["message" => "worker cannot delete this relation"]);
            }

            $worker = Worker::find($id);
            $worker->delete();
            return response()->json(["message" => "success"]);
        }
    }

    public function updateHolidayResponsible()
    {
        Worker::whereIn("id", request("workers"))->update([
            "holiday_responsible" => request("holiday_responsible")
        ]);
        return response()->json(["success" => true]);
    }

    public function getSalary()
    {
        $salarys = Salary::filtered();
        $itemsPerPage = (int) request('itemsPerPage');
        return response()->json(["salary" => $salarys, "data" => $salarys->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 3)]);
    }
    public function addSalary()
    {
        try {
            DB::beginTransaction();
            $salarys = DB::table("salary")->insert([
                "contract_id" => request("contract_id"),
                "salary" => request("salary"),
                "start_date" => request("start"),
                "end_date" => request("end"),
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $salarys]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function editSalary()
    {
        try {
            DB::beginTransaction();
            $salarys = DB::table("salary")->where("id", request("id"))->update([

                "salary" => request("salary"),
                "start_date" => request("start"),
                "end_date" => request("end") ? request("end") : null,
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $salarys]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }

    public function getCommission()
    {
        $commissions = Commission::filtered();
        $itemsPerPage = (int) request('itemsPerPage');
        return response()->json(["data" => $commissions->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 3)]);
    }

    public function addCommission()
    {
        try {
            DB::beginTransaction();
            $commissions = DB::table("commissions")->insert([
                "contract_id" => request("contract_id"),
                "import" => request("import"),
                "type" => request("type"),
                "start_date" => request("start"),
                "end_date" => request("end"),
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $commissions]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }

    public function editCommission()
    {
        try {
            DB::beginTransaction();
            $commissions = DB::table("commissions")->where("id", request("id"))->update([

                "import" => request("import"),
                "type" => request("type"),
                "start_date" => request("start"),
                "end_date" => request("end") ? request("end") : null,
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $commissions]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }

    public function archiveWorker($id)
    {
        $w = Worker::find($id);

        $w->archive = !$w->archive;
        $w->save();
        return ["success" => true];
    }
    public function getSubGestorName(){

        $user = auth()->user();
        $worker = $user->worker;
        $contracts = $worker->contracts;
        $filteredContracts = $contracts->where('end_motive', null);
        $creatorIds = $filteredContracts->pluck('creator_id')->unique();
        $creators = User::find($creatorIds)->pluck('name');
    
        return response()->json([
            "name_subgestor" => $creators,
        ]);
    }

    public function getWorkCenter(Request $request) {
        $company_id = $request->input('company_id');
        $workCenters = WorkCenter::where('company_id', $company_id)->select('id', 'name')->get();
        return response()->json(["data" => $workCenters]);
    }

    public function getMyWorkCenter(Request $request) {
        
        $user             = auth()->user();
        $worker           = $user->worker;
        $nameWorkerCenter = $worker->workCenter;

        return response()->json(["data" => $nameWorkerCenter->name]);
    }

    public function getWorkCompany(Request $request) { //osneida

        $company_id = $request->input('company_id');
        $workersCompany = Contract::select('workers.id', DB::raw("CONCAT(workers.first_name, ' ', workers.last_name) as name"))
        ->join('workers', 'contracts.worker_id', '=', 'workers.id')
        ->where('contracts.company_id', $company_id)
        ->whereNull('contracts.contract_end_date')
        ->orderBy('workers.first_name')
        ->get();
        return response()->json(["data" => $workersCompany]);
    }

          //workers-replace
          public function getWorkerReplaceName($id) { //osneida
            $workersReplace = WorkerReplace::select('worker_id_replace',DB::raw("CONCAT(workers.first_name, ' ', workers.last_name) as name"))
            ->join('workers', 'worker_replaces.worker_id_replace', 'workers.id')
            ->where('worker_replaces.worker_id', $id)
            ->get();
    
            return response()->json(["success" => true, "nombre" => $workersReplace]);
        }
}