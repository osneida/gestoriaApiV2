<?php

namespace App\Console\Commands;

use App\Mail\BaixaMedicaNotification;
use App\Models\Notifications;
use App\Models\WorkerFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyWorkerFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:worker-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info("Entrando para notificar Worker Files");
        try {
            $file = WorkerFile::whereNull("notificated_at")
                ->where("notificacion_attemps", "<", 5)
                ->with(["worker", "worker.companies"])
                ->first();
            if ($file) {
                Log::info(json_encode($file));
                $email = "laboral@ggmanagement.cat";
                $email = env("DEVELOPER_MAIL");
                switch ($file->type) {
                    case "baixa":
                    case "alta":
                        Log::info('Baixa/alta medica');
                        Notifications::create([
                            "text" => "Notificacio de baixa medica del treballador " . $file->worker->first_name . " " . $file->worker->last_name,
                            "type" => "email_baixa_medica",
                            "to" => $email
                        ]);
                        Mail::to($email)->send(new BaixaMedicaNotification($file->worker->companies[0], $file->worker, $file->type, $file->file_route));
                        break;

                    default:
                }
                if (Mail::failures()) {
                    $file->notificacion_attemps += 1;
                } else {
                    $file->notificated_at = now();
                }

                $file->save();
            } else {
                Log::info('No files to notify');
            }
        } catch (\Exception $exception) {
            Log::info("error procesando nóminas: " . $exception->getMessage());
        }
    }
}
