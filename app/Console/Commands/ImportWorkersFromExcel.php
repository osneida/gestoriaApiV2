<?php

namespace App\Console\Commands;

use App\Imports\WorkersImport;
use App\Models\Company;
use App\Models\Worker;
use App\Models\Contract;
use App\Models\Agreement;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportWorkersFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestoria:import-workers-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import initial workers/employees from an excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $collection = Excel::toCollection(new WorkersImport, public_path("excels/llistat-treballadors3.0.xlsx"));
        $sheet = $collection->first();
        $bar = $this->output->createProgressBar(count($sheet) - 1);
        $bar->start();
        $company_workers = [];
        $index = 0;
        $contract_type = ["indefinit" => 1, "indefinit discontinu" => 2, "obra i servei" => 4, "temporal" => 3];
        $working_day_type = ["completa" => 1, "parcial" => 2];
        foreach ($sheet as $item) {
            if ($index > 0 && $item[3] != "" && $item[5] != "") {

                $company = Company::where("slug", Str::slug($item[0]))->first();
                if ($company) {
                    $worker = Worker::create([
                        "first_name" => $item[1],
                        "last_name" => $item[2],
                        "dni" => $item[3],
                        "document_type" => Worker::DNI,
                        "email" => $item[4]
                    ]);

                    array_push($company_workers, [
                        "company_id" => $company->id,
                        "worker_id" => $worker->id
                    ]);
                    Log::info('IMPORTANDO ' . $item[1] . ' ' . $item[2]);

                    $agreement = Agreement::where("name", "like", "%" . $item[11] . "%")->first();
                    Log::info($item[11] . ' - ' . $item[12]);
                    Log::debug(json_encode($agreement));
                    $category = Category::where("name", "like", "%" . $item[12] . "%");
                    if ($agreement) {
                        $category->where("agreement_id", $agreement->id);
                    }

                    $category = $category->first();
                    Log::debug(json_encode($category));
                    $item[6] = strtolower($item[6]);
                    $item[7] = strtolower($item[7]);
                    $data = [
                        "worker_id" => $worker->id,
                        "company_id" => $company->id,
                        "agreement_id" => $agreement ? $agreement->id : null,
                        "category_id" => $category ? $category->id : null,
                        "nss" => $item[5],
                        "contract_type" => $item[6] != "" ? $contract_type[$item[6]] : 1,
                        "working_day_type" => $item[7] != "" ? $working_day_type[$item[7]] : 1,
                        "contract_start_date" => $item[8] ? Date::excelToDateTimeObject((int) $item[8]) : null,
                        "contract_end_date" => $item[9] && $item[9] != "FIN OBRA O SERVEI" ? Date::excelToDateTimeObject((int) $item[9]) : null,
                        "contract_reason" => $item[10] ? $item[10] : null,
                        "salary" => $item[13] === "anual" ? (int) $item[14] : null,
                        "salary_by_hour" => $item[13] === "hores" ? (int) $item[14] : null,
                        "total_hours" => $item[15],
                        "notificated_at" => now()
                    ];

                    if ($item[9] === "FIN OBRA O SERVEI")
                        $data["contract_type"] = 4;
                    Contract::create($data);
                }
            }
            $index++;
            $bar->advance();
        }

        if (count($company_workers) > 0) {
            DB::table("company_worker")->insert($company_workers);
        }
        $bar->finish();
    }
}
