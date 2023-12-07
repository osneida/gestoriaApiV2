<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AltaWorker;
use App\Mail\BaixaContracte;
use App\Models\Agreement;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Modification;
use App\Models\Salary;
use App\Models\Worker;
use DateTime;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use \PDF;

class ContractController extends Controller
{
    /**
     *
     * Prepara datos para el formulario de edición de trabajador + contrato
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function dataForComplexForm(int $id, $company_id)
    {

        if ($company_id != 'undefined') {

            $company = Company::where('id', $company_id)->first();
        } else {

            $company = Company::where("id", auth()->user()->company_id)->first();
        }

        if (!$company) {

            return response()->json(["message" => "company not found"]);
        }

        $exitsWorkerRelation = DB::table("company_worker")->where("company_id", $company->id)->where("worker_id", $id)->first();

        if (!$exitsWorkerRelation) {

            return response()->json(["message" => "company worker relation not found"]);
        }

        $worker = Worker::find($id);

        if (!$worker) {

            return response()->json(["message" => "worker not found"]);
        }

        $contract = Contract::whereWorkerId($id)->whereCompanyId($company->id)->orderBy("id", "desc")->first();

        if (!$contract) {

            $contract = new Contract([
                "company_id" => $company->id,
                "worker_id" => $worker->id,
                "agreement_id" => null,
                "category_id" => null,
                "document_identity_file_a" => null,
                "document_identity_file_b" => null,
                "nss" => null,
                "nss_file" => null,
                "iban" => null,
                "number_of_payments" => null,
                "contract_type" => 1,
                "working_day_type" => 1,
                "contract_end_date" => null,
                "contract_reason" => null,
                "contract_start_date" => null,
                "hours_worked_start" => null,
                "hours_worked_end" => null,
                "creator_id" =>  auth()->user()->id,
                "days_of_holidays" => request("days_of_holidays"),
                "holidays_type" => request("holidays_type"),
                "holidays_location" => request("holidays_location"),
                "temporal_comment" => request("temporal_comment"),
                "observations" => request("observations")
            ]);
        }

        return response()->json(["worker" => $worker, "contract" => $contract]);
    }

    /**
     *
     * Da de alta un trabajador y su contrato
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        Log::info('mensaje', ['data' => request()->all()]);
        if (request()->wantsJson()) {
            $rules = [
                //"agreement" => 'required|exists:agreements,id',
                //"category" => 'required|exists:categories,id',
                "document_identity" => 'required|unique:workers,dni,NULL,id,deleted_at,NULL',
                //"document_identity" => 'required',
                //"document_identity_file_a" => 'required|max:10000',
                //"nss" => "required",
                //"number_of_payments" => "required",
                //"contract_type" => "required",
                //"working_day_type" => "required",
            ];

            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $documentIdentityAFullPath = null;
            $documentIdentityBFullPath = null;
            $nssFullPath = null;
            try {
                DB::beginTransaction();
                if (request("company_id")) {
                    $company = Company::where("id", request("company_id"))->first();
                } else {
                    $company = Company::where("id", auth()->user()->company_id)->first();
                }

                $path = sprintf('%s/%s', 'contracts', $company->slug);

                if (request()->hasFile('document_identity_file_a')) {
                    $documentIdentityAFileName = request()->file("document_identity_file_a")->hashName();
                    $documentIdentityAFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityAFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_a")
                    );
                }

                if (request()->hasFile('document_identity_file_b')) {
                    $documentIdentityBFileName = request()->file("document_identity_file_b")->hashName();
                    $documentIdentityBFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityBFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_b")
                    );
                }
                if (request()->hasFile('nss_file')) {
                    $nssFileName = request()->file("nss_file")->hashName();
                    $nssFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $nssFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("nss_file")
                    );
                }

                $worker = Worker::firstOrCreate([
                    "dni" => request("document_identity")
                ], [
                    "first_name" => request("first_name"),
                    "last_name" => request("last_name"),
                    "dni" => request("document_identity"),
                    "document_type" => "dni",
                    "email" => request("email") != "null" ? request("email") : null,
                    "university" => request("university") === "true",
                    "archive" => false,
                    "auto_archive" => false,
                    "work_center_id" => request("work_center"),
                ]);
                Log::info('mensaje', ['data' => "LLEGOO 1"]);
                DB::table("company_worker")->updateOrInsert([
                    "company_id" => $company->id,
                    "worker_id" => $worker->id
                ], [
                    "company_id" => $company->id,
                    "worker_id" => $worker->id
                ]);

                $agreement = Agreement::find(request("agreement"));
                $data = [
                    "company_id" => $company->id,
                    "worker_id" => $worker->id,
                    "agreement_id" => request("agreement"),
                    "document_identity_file_a" => $documentIdentityAFullPath,
                    "document_identity_file_b" => $documentIdentityBFullPath,
                    "nss" => request("nss"),
                    "nss_file" => $nssFullPath,
                    "iban" => request("iban") != 'null' ? request("iban") : null,
                    "number_of_payments" => request("number_of_payments"),
                    "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                    "contract_type" => request("contract_type"),
                    "working_day_type" => request("working_day_type"),
                    "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                    "contract_end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                    "contract_reason" => request("contract_reason") != 'null' ? request("contract_reason") : null,
                    "contract_start_date" => request("contract_start_date"),
                    "hours_worked_start" => request("hours_worked_start"),
                    "hours_worked_end" => request("hours_worked_end"),
                    "salary" => request("salary") != "null" ? request("salary") : null,
                    "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                    "creator_id" =>  auth()->user()->id,
                    "address" => request("address") != "null" ? request("address") : null,
                    "number" => request("address") != "null" ? request("number") : null,
                    "pis" => request("address") != "null" ? request("pis") : null,
                    "porta" => request("address") != "null" ? request("porta") : null,
                    "postal_code" => request("address") != "null" ? request("postal_code") : null,
                    "city" => request("address") != "null" ? request("city") : null,
                    "days_of_holidays" => $agreement->days_of_holidays,
                    "needs_notification" => request("action") !== 'edit',
                    "holidays_type" => $agreement->holidays_type,
                    "holidays_location" => $company->holidays_location,
                    "temporal_comment" => request("temporal_comment"),
                    "observations" => request("observations")
                ];
                if (request("category") !== 'null') {
                    $data["category_id"] = request("category");
                }
                $contract  = Contract::create($data);
                if (request("salary") !== 'null' && request("salary") !== null && request('salary'))
                    Salary::create([
                        "contract_id" => $contract->id,
                        "salary" => request("salary"),
                        "start_date" => request("contract_start_date"),
                        "end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                    ]);
                Modification::create([
                    "contract_id" => $contract->id,
                    "type" => "Alta",
                    "motive" => null,
                    "start_date" =>  request("contract_start_date"),
                    "editor_id" => auth()->user()->id,
                ]);
                DB::commit();
                return response()->json(["message" => "success"]);
            } catch (\Exception $exception) {
                DB::rollBack();
                if ($documentIdentityAFullPath) {
                    Storage::disk('s3')->delete($documentIdentityAFullPath);
                }
                if ($documentIdentityBFullPath) {
                    Storage::disk('s3')->delete($documentIdentityBFullPath);
                }
                if ($nssFullPath) {
                    Storage::disk('s3')->delete($nssFullPath);
                }
                return response()->json(["message" => env("APP_DEBUG") ? $exception->getMessage() : "Error en el servidor"]);
            }
        }
    }

    /**
     *
     * Actualiza un trabajador y su contrato
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id)
    {
        if (request()->wantsJson()) {
            $worker = Worker::find($id);
            $rules = [
                //"agreement" => 'required|exists:agreements,id',
                //"category" => 'required|exists:categories,id',
                "document_identity" => 'required|unique:workers,dni,' . $worker->id . ',id,deleted_at,NULL',
                //"nss" => "required",
                //"number_of_payments" => "required",
                //"contract_type" => "required",
                //"working_day_type" => "required",
            ];

            if (request("has_contract_id") === "false") {
                $rules["document_identity_file_a"] = 'required';
            }

            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $documentIdentityAOldFullPath = request("document_identity_file_a_pre_uploaded") === "null" ? null : request("document_identity_file_a_pre_uploaded");
            $documentIdentityAFullPath = null;
            $documentIdentityBFullPath = null;
            $documentIdentityBOldFullPath = request("document_identity_file_b_pre_uploaded") === "null" ? null : request("document_identity_file_b_pre_uploaded");
            $nssFullPath = null;
            $nssOldFullPath = request("nss_file_pre_uploaded") === "null" ? null : request("nss_file_pre_uploaded");

            try {
                DB::beginTransaction();

                if (request("company_id")) {
                    $company = Company::where("id", request("company_id"))->first();
                } else {
                    $company = Company::where("id", auth()->user()->company_id)->first();
                }
                $path = sprintf('%s/%s', 'contracts', $company->slug);

                if (request()->hasFile("document_identity_file_a")) {
                    $documentIdentityAFileName = request()->file("document_identity_file_a")->hashName();
                    $documentIdentityAFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityAFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_a")
                    );
                }

                if (request()->hasFile("document_identity_file_b")) {
                    $documentIdentityBFileName = request()->file("document_identity_file_b")->hashName();
                    $documentIdentityBFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityBFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_b")
                    );
                }

                if (request()->hasFile("nss_file")) {
                    $nssFileName = request()->file("nss_file")->hashName();
                    $nssFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $nssFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("nss_file")
                    );
                }

                // actualizamos los datos del trabajador
                $worker->dni = request("document_identity");
                $worker->first_name = request("first_name");
                $worker->last_name = request("last_name");
                $worker->email = request("email") !== "null" ? request("email") : null;
                $worker->university = request("university") === "true" || request("university") === "1";
                $worker->work_center_id = request("work_center");

                $worker->archive = false;
                $worker->auto_archive = false;
                $worker->save();

                $agreement = Agreement::find(request("agreement"));
                $contractInput = [
                    "company_id" => $company->id,
                    "worker_id" => $worker->id,
                    "agreement_id" => request("agreement"),
                    "document_identity_file_a" => $documentIdentityAFullPath ? $documentIdentityAFullPath : $documentIdentityAOldFullPath,
                    "document_identity_file_b" => $documentIdentityBFullPath ? $documentIdentityBFullPath : $documentIdentityBOldFullPath,
                    "nss" => request("nss"),
                    "nss_file" => $nssFullPath ? $nssFullPath : $nssOldFullPath,
                    "iban" => request("iban") != 'null' ? request("iban") : null,
                    "number_of_payments" => request("number_of_payments"),
                    "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                    "contract_type" => request("contract_type"),
                    "working_day_type" => request("working_day_type"),
                    "contract_end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                    "contract_reason" => request("contract_reason") != 'null' ? request("contract_reason") : null,
                    "contract_start_date" => request("contract_start_date"),
                    "hours_worked_start" => request("hours_worked_start"),
                    "hours_worked_end" => request("hours_worked_end"),
                    "salary" => request("salary") != "null" ? request("salary") : null,
                    "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                    //"creator_id" =>  auth()->user()->id,
                    "address" => request("address") != "null" ? request("address") : null,
                    "number" => request("address") != "null" ? request("number") : null,
                    "pis" => request("address") != "null" ? request("pis") : null,
                    "porta" => request("address") != "null" ? request("porta") : null,
                    "postal_code" => request("address") != "null" ? request("postal_code") : null,
                    "city" => request("address") != "null" ? request("city") : null,
                    "days_of_holidays" => $agreement->days_of_holidays,
                    "needs_notification" => request("action") !== 'edit',
                    "holidays_type" => $agreement->holidays_type,
                    "holidays_location" => $company->holidays_location,
                    "temporal_comment" => request("temporal_comment"),
                    "observations" => request("observations"),
                ];
                if (request("category") !== 'null') {
                    $contractInput["category_id"] = request("category");
                }
                // actualizamos los datos del contrato
                if (request("contract_id") && request("contract_id") != 'undefined') {

                    Contract::find(request("contract_id"))->update($contractInput);
                    if (request("salary") !== 'null')

                        Salary::updateOrCreate(
                            ["contract_id" => request("contract_id")],
                            [
                                "salary" => request("salary"),
                                "start_date" => request("contract_start_date"),
                                "end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                            ]
                        );
                    Modification::create([
                        "contract_id" => request("contract_id"),
                        "type" => "Modificació",
                        "motive" => self::typeMotive(request("mod_type"), false),
                        "start_date" =>  request("contract_start_date"),
                        "editor_id" => auth()->user()->id,
                    ]);
                } else {
                    $idContract = Contract::create($contractInput);
                    if (request("salary") !== 'null')
                        Salary::create([
                            "contract_id" => $idContract->id,
                            "salary" => request("salary"),
                            "start_date" => request("contract_start_date"),
                            "end_date" => request("contract_end_date") ? request("contract_end_date") : now(),
                        ]);
                    Modification::create([
                        "contract_id" => $idContract->id,
                        "type" => "Modificació",
                        "motive" => self::typeMotive(request("mod_type"), false),
                        "start_date" =>  request("contract_start_date"),
                        "editor_id" => auth()->user()->id,
                    ]);
                }

                if (request("company_id_old") && request("company_id_old") != 'undefined') {
                //osneida, para modificar la tabla company_worker
                if (request("company_id_old") != request("company_id")) {
                    $workerCompany = DB::table("company_worker")->where("worker_id", $id)
                                                                ->where("company_id", request("company_id_old"));
                    $workerCompany->update([
                        "company_id" => request("company_id")
                    ]);
                }
                }

                if(request("worker_id_replace")){ //osneida para modificar trabajador a quien reemplaza
                    DB::table("worker_replaces")->updateOrInsert([
                        "worker_id"         => $id
                    ],[
                        "worker_id_replace" => request("worker_id_replace"), //a quien remplaza
                        "company_id"        => request("company_id"),
                    ]);
                }

                DB::commit();

                // eliminamos los archivos antiguos si se han subido de nuevos
                $this->_deleteContractFileIfExists($documentIdentityAOldFullPath);
                $this->_deleteContractFileIfExists($documentIdentityBOldFullPath);
                $this->_deleteContractFileIfExists($nssOldFullPath);

                return response()->json(["message" => "success"]);
            } catch (\Exception $exception) {
                DB::rollBack();

                // eliminamos los archivos nuevos si algo ha fallado respetando los antiguos
                $this->_deleteContractFileIfExists($documentIdentityAFullPath);
                $this->_deleteContractFileIfExists($documentIdentityBFullPath);
                $this->_deleteContractFileIfExists($nssFullPath);
                return response()->json(["message" => env("APP_DEBUG") ? $exception->getMessage() : "Error en el servidor"]);
            }
        }
    }


    public function modificateLastContract(int $id)
    {
        try {
            DB::beginTransaction();
            $worker = Worker::find($id);

            $rules = [
                "agreement" => '',
                "category" => '',
                "document_identity" => 'required|unique:workers,dni,' . $worker->id . ',id,deleted_at,NULL',
                "nss" => "required",
                "number_of_payments" => "required",
                "contract_type" => "required",
                "working_day_type" => "required",
            ];

            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {

                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $lastContract = $worker->latestContract()->first();

            $lastContract->update(["contract_end_date" => request("contract_start_date")]);

            $total_renovations = $lastContract->renovation_count;
            $total_month = $lastContract->month_renovation_count;
            if (request("mod_type") === 2) {

                $total_renovations++;

                $start = new DateTime($lastContract->contract_start_date);

                $end = new DateTime($lastContract->contract_end_date);

                $total_month += $start->diff($end)->m;
            }

            $documentIdentityAOldFullPath = request("document_identity_file_a_pre_uploaded");

            $documentIdentityBOldFullPath = request("document_identity_file_b_pre_uploaded");;

            $nssOldFullPath = request("nss_file_pre_uploaded");;

            //try {
            $company = Company::where("id", request("company_id"))->first();

            // actualizamos los datos del trabajador
            $worker->dni = request("document_identity");
            $worker->first_name = request("first_name");
            $worker->last_name = request("last_name");
            $worker->email = request("email") != "null" ? request("email") : null;

            $worker->archive = false;
            $worker->auto_archive = false;
            $worker->save();

            $agreement = Agreement::find(request("agreement"));


            $contractInput = [
                "company_id" => $company->id,
                "worker_id" => $worker->id,
                "agreement_id" => request("agreement"),
                "category_id" => request("category"),
                "document_identity_file_a" =>  $documentIdentityAOldFullPath,
                "document_identity_file_b" => $documentIdentityBOldFullPath,
                "nss" => request("nss"),
                "nss_file" => $nssOldFullPath,
                "iban" => request("iban") != 'null' ? request("iban") : null,
                "number_of_payments" =>   $worker->latestContract()->first()->number_of_payments,
                "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                "contract_type" => request("contract_type"),
                "working_day_type" => request("working_day_type"),
                "contract_end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                "contract_reason" => request("contract_reason") != 'null' ? request("contract_reason") : null,
                "contract_start_date" => request("contract_start_date"),
                "hours_worked_start" => request("hours_worked_start"),
                "hours_worked_end" => request("hours_worked_end"),
                "salary" => request("salary") != "null" ? request("salary") : null,
                "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                //"creator_id" =>  auth()->user()->id,
                "modificated_contract_id" => $worker->latestContract()->first()->id,
                "renovation_count" => $total_renovations,
                "month_renovation_count" => $total_month,
                "end_motive" => request("mod_type"),
                "days_of_holidays" => $agreement->days_of_holidays,
                "address" => request("address") != "null" ? request("address") : null,
                "number" => request("address") != "null" ? request("number") : null,
                "pis" => request("address") != "null" ? request("pis") : null,
                "porta" => request("address") != "null" ? request("porta") : null,
                "postal_code" => request("address") != "null" ? request("postal_code") : null,
                "city" => request("address") != "null" ? request("city") : null,
                "needs_notification" => request("action") !== 'edit',
                "holidays_type" => $agreement->holidays_type,
                "holidays_location" => $company->holidays_location,
                "temporal_comment" => request("temporal_comment"),
                "observations" => request("observations")
            ];

            $contract = Contract::create($contractInput);
            $salary = Salary::where("contract_id", $lastContract->id)->orderBy('start_date', 'asc')->first();
            if ($salary->salary == request("salary")) {
                Salary::where("contract_id", $lastContract->id)->update([
                    "contract_id" =>  $contract->id,
                    "start_date" => request("contract_start_date"),
                    "end_date" => request("contract_end_date"),
                ]);
                Modification::create([
                    "contract_id" => $lastContract->id,
                    "type" => "Modificació",
                    "motive" => self::typeMotive(request("mod_type"), false),
                    "start_date" =>  request("contract_start_date"),
                    "editor_id" => auth()->user()->id,
                ]);
            } else {
                Salary::create([
                    "contract_id" =>  $contract->id,
                    "salary" => request("salary"),
                    "start_date" => request("contract_start_date"),
                    "end_date" => request("contract_end_date"),
                ]);
                Modification::create([
                    "contract_id" => $contract->id,
                    "type" => "Modificació",
                    "motive" =>  self::typeMotive(request("mod_type"), false),
                    "start_date" =>  request("contract_start_date"),
                    "editor_id" => auth()->user()->id,
                ]);
            }
            DB::commit();


            return response()->json(["message" => "success"]);
        } catch (\Exception $exception) {

            DB::rollBack();
            // eliminamos los archivos nuevos si algo ha fallado respetando los antiguos
            return response()->json(["message" => env("APP_DEBUG") ? $exception->getMessage() : "Error en el servidor"]);
        }
    }
    private static function typeMotive($value, $from)
    {
        switch ($value) {
            case 1:

                return $from ? "Voluntària" : "Hores";
                break;
            case 2:

                return $from ? "Fi de contracte" : "Renovació";
                break;
            case 3:

                return $from ? "Acomiadament" : "Transformació";
                break;
            case 4:

                return "Categoria";
                break;
        }
    }

    public function modificationLastContract(int $id)
    {
        try {
            DB::beginTransaction();
            $worker = Worker::find($id);

            $rules = [
                "agreement" => '',
                "category" => '',
                "document_identity" => 'required|unique:workers,dni,' . $worker->id . ',id,deleted_at,NULL',
                "nss" => "required",
                "number_of_payments" => "required",
                "contract_type" => "required",
                "working_day_type" => "required",
            ];

            $validator = Validator::make(request()->all(), $rules);
            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $lastContract = $worker->latestContract()->first();



            $total_renovations = $lastContract->renovation_count;
            $total_month = $lastContract->month_renovation_count;
            if (request("mod_type") === 2) {
                $total_renovations++;
                $start = new DateTime($lastContract->contract_start_date);
                $end = new DateTime($lastContract->contract_end_date);

                $total_month += $start->diff($end)->m;
            }

            $documentIdentityAOldFullPath = request("document_identity_file_a_pre_uploaded");

            $documentIdentityBOldFullPath = request("document_identity_file_b_pre_uploaded");;

            $nssOldFullPath = request("nss_file_pre_uploaded");;

            //try {
            $company = Company::where("id", request("company_id"))->first();

            // actualizamos los datos del trabajador
            $worker->dni = request("document_identity");
            $worker->first_name = request("first_name");
            $worker->last_name = request("last_name");
            $worker->email = request("email") != "null" ? request("email") : null;
            $worker->save();

            $agreement = Agreement::find(request("agreement"));



            $contract = Contract::where("id", request("contract_id"))->update([
                "company_id" => $company->id,
                "worker_id" => $worker->id,
                "agreement_id" => request("agreement"),
                "category_id" => request("category"),
                "document_identity_file_a" =>  $documentIdentityAOldFullPath,
                "document_identity_file_b" => $documentIdentityBOldFullPath,
                "nss" => request("nss"),
                "nss_file" => $nssOldFullPath,
                "iban" => request("iban") != 'null' ? request("iban") : null,
                "number_of_payments" =>   $worker->latestContract()->first()->number_of_payments,
                "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                "contract_type" => request("contract_type"),
                "working_day_type" => request("working_day_type"),
                "contract_end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                "contract_reason" => request("contract_reason") != 'null' ? request("contract_reason") : null,
                "contract_start_date" => request("contract_start_date"),
                "hours_worked_start" => request("hours_worked_start"),
                "hours_worked_end" => request("hours_worked_end"),
                "salary" => request("salary") != "null" ? request("salary") : null,
                "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                "address" => request("address") != "null" ? request("address") : null,
                "number" => request("address") != "null" ? request("number") : null,
                "pis" => request("address") != "null" ? request("pis") : null,
                "porta" => request("address") != "null" ? request("porta") : null,
                "postal_code" => request("address") != "null" ? request("postal_code") : null,
                "city" => request("address") != "null" ? request("city") : null,
                //"creator_id" =>  auth()->user()->id,
                "modificated_contract_id" => $worker->latestContract()->first()->id,
                "renovation_count" => $total_renovations,
                "month_renovation_count" => $total_month,
                "end_motive" => request("mod_type"),
                "days_of_holidays" => $agreement->days_of_holidays,
                "holidays_type" => $agreement->holidays_type,
                "holidays_location" => $company->holidays_location,
                "temporal_comment" => request("temporal_comment"),
                "observations" => request("observations"),
                "needs_notification" => true,
            ]);
            $salary = Salary::where("contract_id", request("contract_id"))->update([

                "contract_id" =>  request("contract_id"),
                "salary" => request("salary"),
                "start_date" => request("contract_start_date"),
                "end_date" => request("contract_end_date"),
            ]);
            Modification::create([
                "contract_id" =>  request("contract_id"),
                "type" => "Modificació",
                "motive" => self::typeMotive(request("mod_type"), false),
                "start_date" =>  request("contract_start_date"),
                "editor_id" => auth()->user()->id,
            ]);

            DB::commit();


            return response()->json(["message" => "success"]);
        } catch (\Exception $exception) {

            DB::rollBack();
            // eliminamos los archivos nuevos si algo ha fallado respetando los antiguos
            return response()->json(["message" => env("APP_DEBUG") ? $exception->getMessage() : "Error en el servidor"]);
        }
    }

    public function baixaContract($id_contract)
    {

        $contract = Contract::where("id", $id_contract)->with("worker", "company")->first();

        $contract->contract_end_date = request("contract_end_date");

        $contract->enjoyed_vacancies = request("enjoyed_vacancies") != 'null' ? request("enjoyed_vacancies") : null;

        $contract->not_enjoyed_vacancies = request("not_enjoyed_vacancies") != 'null' ? request("not_enjoyed_vacancies") : null;

        $contract->end_motive = request("end_motive");

        if (request()->hasFile("cart")) {

            //Guardamos archivo
            $file = request()->file("cart");
            $filePath = sprintf("%s/%s", $contract->worker->dni, $file->hashName());
            Storage::disk("s3")->put($contract->worker->dni, $file);
            $contract->baixa_voluntaria_file = $filePath;
        }

        $contract->contract_end_comunication_date = request("contract_end_comunication_date");
        $contract->needs_notification = true;
        $contract->save();
        Modification::create([
            "contract_id" =>  request("contract_id"),
            "type" => "Baixa",
            "motive" => self::typeMotive(request("end_motive"), true),
            "start_date" =>  request("contract_start_date"),
            "editor_id" => auth()->user()->id,
        ]);
        /*if (request('end_motive') != 2) {
            Mail::to(env("DEVELOPER_MAIL"))->send(new BaixaContracte($contract, $contract->worker, $contract->company, request('end_motive'), [$file]));
        }*/


        return response()->json(["message" => "succes"], 200);
    }

    /**
     *
     * Crea un nuevo contrato para un trabajador
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewContract(int $id)
    {
        Log::info('mensajeeeeee prueba', ['data' => request()->all()]);
        if (request()->wantsJson()) {
            $worker = Worker::find($id);

            $rules = [
                "agreement" => 'required|exists:agreements,id',
                "category" => 'required|exists:categories,id',
                "document_identity" => 'required|unique:workers,dni,' . $worker->id . ',id,deleted_at,NULL',
                "nss" => "required",
                "number_of_payments" => "required",
                "contract_type" => "required",
                "working_day_type" => "required"
            ];

            $documentIdentityAOldFullPath = request("document_identity_file_a_pre_uploaded");

            if (!$documentIdentityAOldFullPath || $documentIdentityAOldFullPath === "null") {
                $rules["document_identity_file_a"] = 'required|max:10000';
            }

            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {

                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $documentIdentityAFullPath = null;
            $documentIdentityAOldFullPath = request("document_identity_file_a_pre_uploaded");

            $documentIdentityBFullPath = null;
            $documentIdentityBOldFullPath = request("document_identity_file_b_pre_uploaded");

            $nssFullPath = null;
            $nssOldFullPath = request("nss_file_pre_uploaded");

            try {
                DB::beginTransaction();
                if (request("company_id")) {
                    $company = Company::where("id", request("company_id"))->first();
                } else {
                    $company = Company::where("id", auth()->user()->company_id)->first();
                }
                $path = sprintf('%s/%s', 'contracts', $company->slug);

                if (request()->hasFile("document_identity_file_a")) {
                    $documentIdentityAFileName = request()->file("document_identity_file_a")->hashName();
                    $documentIdentityAFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityAFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_a")
                    );
                }

                if (request()->hasFile("document_identity_file_b")) {
                    $documentIdentityBFileName = request()->file("document_identity_file_b")->hashName();
                    $documentIdentityBFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $documentIdentityBFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("document_identity_file_b")
                    );
                }

                if (request()->hasFile("nss_file")) {
                    $nssFileName = request()->file("nss_file")->hashName();
                    $nssFullPath = sprintf(
                        '%s/%s',
                        $path,
                        $nssFileName
                    );
                    Storage::disk('s3')->put(
                        $path,
                        request()->file("nss_file")
                    );
                }

                // actualizamos los datos del trabajador
                $worker->dni = request("document_identity");
                $worker->first_name = request("first_name");
                $worker->last_name = request("last_name");
                $worker->email = request("email") != "null" ? request("email") : null;
                $workCenter = json_decode(request("work_center"), true);
                Log::info('mensajeeeeee prueba work centerrrrrr', ['data' => $workCenter]);
                $worker->work_center_id = $workCenter;

                $worker->archive = false;
                $worker->auto_archive = false;
                $worker->save();

                $agreement = Agreement::find(request("agreement"));

                $contractInput = [
                    "company_id" => $company->id,
                    "worker_id" => $worker->id,
                    "agreement_id" => request("agreement"),
                    "category_id" => request("category"),
                    "document_identity_file_a" => $documentIdentityAFullPath ? $documentIdentityAFullPath : $documentIdentityAOldFullPath,
                    "document_identity_file_b" => $documentIdentityBFullPath ? $documentIdentityBFullPath : $documentIdentityBOldFullPath,
                    "nss" => request("nss"),
                    "nss_file" => $nssFullPath ? $nssFullPath : $nssOldFullPath,
                    "iban" => request("iban") != 'null' ? request("iban") : null,
                    "number_of_payments" => request("number_of_payments"),
                    "total_hours" => request("total_partial_hours") != "null" ? request("total_partial_hours") : null,
                    "contract_type" => request("contract_type"),
                    "working_day_type" => request("working_day_type"),
                    "contract_end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                    "contract_reason" => request("contract_reason") != 'null' ? request("contract_reason") : null,
                    "contract_start_date" => request("contract_start_date"),
                    "hours_worked_start" => request("hours_worked_start"),
                    "hours_worked_end" => request("hours_worked_end"),
                    "salary" => request("salary") != "null" ? request("salary") : null,
                    "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                    //"creator_id" =>  auth()->user()->id,
                    "address" => request("address") != "null" ? request("address") : null,
                    "number" => request("address") != "null" ? request("number") : null,
                    "pis" => request("address") != "null" ? request("pis") : null,
                    "porta" => request("address") != "null" ? request("porta") : null,
                    "postal_code" => request("address") != "null" ? request("postal_code") : null,
                    "city" => request("address") != "null" ? request("city") : null,
                    "salary" => request("salary") != "null" ? request("salary") : null,
                    "salary_by_hour" => request("salary_by_hour")  != "null" ? request("salary_by_hour") : null,
                    //"creator_id" =>  auth()->user()->id,
                    "days_of_holidays" => $agreement->days_of_holidays,
                    "needs_notification" => request("action") !== 'edit',
                    "holidays_type" => $agreement->holidays_type,
                    "holidays_location" => $company->holidays_location,
                    "temporal_comment" => request("temporal_comment"),
                    "observations" => request("observations"),
                ];

                // actualizamos los datos del contrato
                $contract = Contract::create($contractInput);
                $contract = Contract::where("id", $contract->id)->with(["category"])->first();
                $contract->hours_worked_start = json_decode($contract->hours_worked_start, true);
                $contract->hours_worked_end = json_decode($contract->hours_worked_end, true);

                if (request("salary") !== 'null' && request("salary") !== null && request('salary'))

                    Salary::create([
                        "contract_id" => $contract->id,
                        "salary" => request("salary"),
                        "start_date" => request("contract_start_date"),
                        "end_date" => request("contract_end_date") != 'null' ? request("contract_end_date") : null,
                    ]);

                Modification::create([
                    "contract_id" => $contract->id,
                    "type" => "Alta",
                    "motive" => null,
                    "start_date" =>  request("contract_start_date"),
                    "editor_id" => auth()->user()->id,
                ]);

                DB::commit();

                // eliminamos los archivos antiguos si se han subido de nuevos

                $this->_deleteContractFileIfExists($documentIdentityAOldFullPath);
                $this->_deleteContractFileIfExists($documentIdentityBOldFullPath);
                $this->_deleteContractFileIfExists($nssOldFullPath);

                //$pdf = PDF::loadView("pdf.alta", compact("worker"), compact("contract"));

                return response()->json(["message" => "success"]);
            } catch (\Exception $exception) {

                DB::rollBack();

                // eliminamos los archivos nuevos si algo ha fallado respetando los antiguos
                $this->_deleteContractFileIfExists($documentIdentityAFullPath);
                $this->_deleteContractFileIfExists($documentIdentityBFullPath);
                $this->_deleteContractFileIfExists($nssFullPath);

                return response()->json(["message" => env("APP_DEBUG") ? $exception->getMessage() : "Error en el servidor"]);
            }
        }
    }
    /**
     *
     * Elimina archivos de s3
     *
     * @param $fullPath
     */
    protected function _deleteContractFileIfExists($fullPath)
    {
        if ($fullPath) {
            Storage::disk('s3')->delete($fullPath);
        }
    }

    function finiquitoPayed($id)
    {

        return Contract::where("id", $id)->update(["finiquito_payed" => now()]);
    }


    function pdfTest()
    {

        $data = [];

        $pdf = PDF::loadView("pdf.alta", compact("data"))->save(public_path("alta_test.pdf"));
    }

    public function mycontracts()
    {
        $contracts = Contract::where('worker_id', auth()->user()->worker_id)
            ->whereDate("contract_start_date", "<=", now())
            ->where(function ($q) {
                $q->whereDate("contract_end_date", ">", now());
                $q->orWhereNull("contract_end_date");
            })
            ->with(["contractType", "company"])
            ->get();
        return response()->json($contracts);
    }


    public function updateManagerResponsible()
    {

        try {
            $rules = [
                'workers' => 'required',
                'manager_responsible' => 'required',

            ];


            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            foreach (request("workers") as $worker_id) {
                $contracts = Contract::where('worker_id', $worker_id)->first();
                $contracts->update([
                    "creator_id" => request("manager_responsible"),

                ]);
            }


            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "data" => $exception->getMessage()], 400);
        }
    }
    public function workerContracts() //osneida
    {
        $id = request("worker_id");
        $select   =[];
        $select2  =[];
        $companys = Contract::select('worker_id', 'company_id')->where("worker_id", $id)->with("company")->get();
        foreach ($companys as $compa) {
            $select['worker_id']  = $compa->worker_id;
            $select['company_id'] = $compa->company_id;
            $select['id']         = $compa->company_id;
            $select['name']       = $compa->company->name;

            array_push($select2,$select); 
        }
        return response()->json(["success" => true, "data" => $select2]);
    }
}
