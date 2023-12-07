<?php

namespace App\Console\Commands;

use App\Mail\ContractExpires;
use App\Models\Contract;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyCompanyByMailBeforeContractExpires extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestoria:notify-company-by-mail-before-contract-expires';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo electrónico a la empresa notificando que un contrato de un trabajador suyo está próximo a expirar';

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
        Log::info("Entrando para procesar alertas por contratos a punto de expirar");
        try {
            $contract = Contract::with(['worker', 'company.user'])
                ->where('contract_end_date', '<=', now()->addDays(Contract::DAYS_FOR_SEND_NOTIFICATIONS))
                ->whereNull("send_expired_notification")
                ->where("attempts_send_expired_notification", "<", Contract::MAX_ATTEMPTS)
                ->where("contract_type", "!=", Contract::INDEFINITE);

            $contractQuery = with($contract)->first();
            $contractUpdate = with($contract);
            if ($contractQuery) {
                if ( ! $contractQuery->company) {
                    $contractUpdate->update([
                        "send_expired_notification" => now(),
                        "reason_not_send_expired_notification" => 'La empresa no existe',
                        "attempts_send_expired_notification" => $contractQuery->attempts_send_expired_notification += 1
                    ]);
                    return;
                }
                if ( ! $contractQuery->company->user) {
                    $contractUpdate->update([
                        "send_expired_notification" => now(),
                        "reason_not_send_expired_notification" => 'La empresa no tiene correo electrónico',
                        "attempts_send_expired_notification" => $contractQuery->attempts_send_expired_notification += 1
                    ]);
                    return;
                }
                if ( ! $contractQuery->worker) {
                    $contractUpdate->update([
                        "send_expired_notification" => now(),
                        "reason_not_send_expired_notification" => 'El trabajador no existe',
                        "attempts_send_expired_notification" => $contractQuery->attempts_send_expired_notification += 1
                    ]);
                    return;
                }

                $subject = "Contracte a punt d'expirar";
                $message = "El contracte per al treballador {$contractQuery->worker->first_name} {$contractQuery->worker->last_name} està a punt d'expirar." . PHP_EOL;
                $message .= "Si us plau, accediu a l'aplicatiu i reviseu que tot sigui correcte, en cas de renovació contacteu amb la gestoría.";
                $email = app()->environment('production') ? $contractQuery->company->user->email : env("DEVELOPER_MAIL");
                Mail::to($email)->locale('ca')->send(
                    new ContractExpires($subject, $message)
                );

                if (Mail::failures()) {
                    Log::info("error notificando a la empresa por correo electrónico");
                    $contractUpdate->update([
                        "send_expired_notification" => null,
                        "reason_not_send_expired_notification" => null,
                        "attempts_send_expired_notification" => $contractQuery->attempts_send_expired_notification += 1
                    ]);
                    return;
                }

                $contractUpdate->update([
                    "send_expired_notification" => now(),
                    "reason_not_send_expired_notification" => null
                ]);
            }
        } catch (\Exception $exception) {
            Log::info("error procesando la petición: " . $exception->getMessage());
        }
    }
}
