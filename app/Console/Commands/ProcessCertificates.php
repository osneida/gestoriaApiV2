<?php

namespace App\Console\Commands;

use App\Mail\NoMailTryNotification;
use App\Mail\Certificates;
use App\Models\Certificate;
use App\Models\CertificatePeriod;
use App\Models\Setting;
use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestoria:process-certificates';

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
        Log::info("Entrando para procesar certificados");
        try {
            $period = date("Y", strtotime("now"));
            $lastYearPeriod = date("Y", strtotime("now - 1 year"));
            Log::info("Procesando periodos: $period y $lastYearPeriod");



            $certificate = Certificate::whereHas("worker", function ($q) {
                $q->whereNotNull("email");
                $q->whereHas('contracts', function ($query) {
                    /*
                    $query->whereDate('contract_start_date', '<=', now());
                    $query->where(function ($query) {
                        $query->whereDate('contract_end_date', '>', now())->orWhereNull('contract_end_date');
                    })
                    ;*/
                });
            })
                ->whereIn("period", [$period, $lastYearPeriod])
                ->whereNull("processed")
                ->with("company")
                ->where("attempts", "<", Certificate::MAX_ATTEMPTS);

            $certificateQuery = with($certificate)->first();

            // HAY NÓMINAS QUE PROCESAR
            if ($certificateQuery) {
                $worker = Worker::find($certificateQuery->worker_id);
                if ($worker) {

                    $subject =  "Certificat";
                    $message = $certificateQuery->company->workers_access ? "Hola, ja te el certificat de retencions i ingressos a compte de l’impost sobre la Renta de les Persones Fisiques al portal" : "Hola, adjuntem certificat de retencions i ingressos a compte de l’impost sobre la Renta de les Persones Fisiques";

                    $data = [];
                    array_push($data, [
                        "s3_path" => $certificateQuery->document_file,
                        "period" => sprintf('certificat-%s.pdf', $certificateQuery->period)
                    ]);


                    $email = app()->environment('production') ? $worker->email : env("DEVELOPER_MAIL");
                    Mail::to($email)->locale('ca')->send(
                        new Certificates($subject, $message, $data, !$certificateQuery->company->workers_access)
                    );

                    if (Mail::failures()) {
                        Log::info("error enviando el correo nóminas");
                        Certificate::where('id', $certificateQuery->id)->update([
                            "processed" => null,
                            "updated_at" => now(),
                            "attempts" => $certificateQuery->attempts += 1
                        ]);
                        Mail::to(env("DEVELOPER_MAIL"))->send(new NoMailTryNotification(
                            $worker

                        ));
                        return;
                    }

                    Certificate::where('id', $certificateQuery->id)->update([
                        "processed" => now(),
                        "attempts" => 0
                    ]);

                    Log::info(sprintf("nómina para el trabajador %s procesada correctamente", $certificateQuery->worker_id));

                    $certificatesPending = Certificate::whereCompanyId($certificateQuery->company_id)->whereNull("processed")->where("period", $period)->where("attempts", "<", Certificate::MAX_ATTEMPTS)->count();
                    // ¿YA SE HAN PROCESADO TODAS LAS NÓMINAS Y ACTUALIZAMOS TODOS LOS ARCHIVOS PARA ESE PERIODO?
                    if ($certificatesPending === 0) {
                        CertificatePeriod::where("company_id", $certificateQuery->company_id)
                            ->where("period", $period)
                            ->update([
                                "processed" => now()
                            ]);
                    }
                } else {
                    Log::info("no se ha encontrado al trabajador con id " . $certificateQuery->worker_id);
                    CertificatePeriod::where("company_id", $certificateQuery->company_id)->update([
                        "processed" => null,
                        "updated_at" => now(),
                        "attempts" => $certificateQuery->attempts += 1
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
