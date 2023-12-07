<?php

namespace App\Console\Commands;

use App\Mail\NoMailTryNotification;
use App\Mail\Payrolls;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Setting;
use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessMonthlyPayrolls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestoria:process-monthly-payrolls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía las nóminas una vez al mes a cada trabajador';

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
        Log::info("Entrando para procesar nóminas");
        try {
            $period = date("Y-m", strtotime("now"));
            $lastMonthPeriod = date("Y-m", strtotime("now - 1 month"));
            Log::info("Procesando periodos: $period y $lastMonthPeriod");


            $payroll = Payroll::whereHas("worker", function ($q) {
                $q->whereNotNull("email");
                $q->whereHas('contracts', function ($query) {
                    $query->whereDate('contract_start_date', '<=', now());
                    $query->where(function ($query) {
                        $query->whereDate('contract_end_date', '>', now())->orWhereNull('contract_end_date');
                    });
                });
            })
                ->whereIn("period", [$period, $lastMonthPeriod])
                ->whereNull("processed")
                ->where("attempts", "<", Payroll::MAX_ATTEMPTS)
                ->inRandomOrder()
                ->with("company");

            $payrollQuery = with($payroll)->first();


            // HAY NÓMINAS QUE PROCESAR
            if ($payrollQuery) {
                $worker = Worker::find($payrollQuery->worker_id);
                if ($worker) {
                    $subject = "Nòmina";
                    $message = "Adjuntem nòmina mensual";

                    $data = [];

                    $settingSubject = Setting::select("val")->where("key", "subject_payrolls")->first();
                    $settingMessage = Setting::select("val")->where("key", "message_payrolls")->first();
                    if ($settingSubject) {
                        $subject = $settingSubject->val;
                    }
                    if ($settingMessage && $payrollQuery->company->workers_access) {
                        $message = $settingMessage->val;
                        array_push($data, [ //para que no envíe el pdf adjunto cuando tiene acceso al portal => workers_access = 1, osneida
                            "s3_path" => '',
                            "period"  => ''
                        ]);
                    }else
                    {
                        array_push($data, [
                            "s3_path" => $payrollQuery->document_file,
                            "period"  => sprintf('nòmina-%s.pdf', $payrollQuery->period)
                        ]);
    
                    }

                    $email = app()->environment('production') ? $worker->email : env("DEVELOPER_MAIL");
                    Mail::to($email)->locale('ca')->send(
                        new Payrolls($subject, $message, $data, $payrollQuery->company->workers_access)
                    );

                    if (Mail::failures()) {
                        Log::info("error enviando el correo nóminas");
                        Payroll::where('id', $payrollQuery->id)->update([
                            "processed" => null,
                            "updated_at" => now(),
                            "attempts" => $payrollQuery->attempts += 1
                        ]);
                        Mail::to(env("DEVELOPER_MAIL"))->send(new NoMailTryNotification(
                            $worker
                        ));
                        return;
                    }

                    Payroll::where('id', $payrollQuery->id)->update([
                        "processed" => now(),
                        "attempts" => 0
                    ]);

                    Log::info(sprintf("nómina para el trabajador %s procesada correctamente", $payrollQuery->worker_id));

                    $payrollsPending = Payroll::whereCompanyId($payrollQuery->company_id)->whereNull("processed")->where("period", $period)->where("attempts", "<", Payroll::MAX_ATTEMPTS)->count();
                    // ¿YA SE HAN PROCESADO TODAS LAS NÓMINAS Y ACTUALIZAMOS TODOS LOS ARCHIVOS PARA ESE PERIODO?
                    if ($payrollsPending === 0) {
                        PayrollPeriod::where("company_id", $payrollQuery->company_id)
                            ->where("period", $period)
                            ->update([
                                "processed" => now()
                            ]);
                    }
                } else {
                    Log::info("no se ha encontrado al trabajador con id " . $payrollQuery->worker_id);
                    Payroll::where('id', $payrollQuery->id)->update([
                        "processed" => null,
                        "updated_at" => now(),
                        "attempts" => $payrollQuery->attempts += 1
                    ]);
                }
            } else {
                Log::info("no hay nóminas que procesar");
            }
        } catch (\Exception $exception) {
            Log::info("error procesando nóminas: " . $exception->getMessage());
        }
    }
}
