<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\NotificationsController;
use App\Mail\BaixaContracte;
use App\Mail\ModificateContract;
use App\Models\Contract;
use App\Models\Notifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\Contract\Alta;
use App\Notifications\Contract\Modification;
use App\Notifications\Contract\Baixa;
use \PDF;

class NotifyContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:notify-contract';

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
        $contract =
            Contract::where("needs_notification", true)
            ->with(["company", "worker", "category"])
            ->inRandomOrder()
            ->first();

        Log::debug($contract);

        //dd('estoy',$contract);

        if ($contract) {
            $to = env("DEVELOPER_MAIL");
            Log::info(json_encode($contract));
            if ($contract->not_enjoyed_vacancies !== null || $contract->enjoyed_vacancies) {
                Log::info("Notificar Baixa");
                NotificationsController::notificateRole(new Baixa($contract, $contract->worker));
            } else if ($contract->modificated_contract_id !== null) {
                Log::info("Notificar Modificacio");

                NotificationsController::notificateRole(new Modification($contract, $contract->worker));
            } else {
                Log::info("Notificar Alta");
                $worker = $contract->worker;
                $pdf = PDF::loadView("pdf.alta", compact("worker"), compact("contract"));
                NotificationsController::notificateRole(new Alta($contract, $worker, $pdf));
            }

            $contract->needs_notification = false;
            $contract->notificated_at = now();
            $contract->save();
        } else {
            Log::info("No hay contrato para notificar");
        }
    }
}
