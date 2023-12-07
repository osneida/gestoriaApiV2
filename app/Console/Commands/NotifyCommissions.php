<?php

namespace App\Console\Commands;

use App\Mail\CommissionsNotifications;
use App\Mail\NoMailNotification;
use App\Models\Commission;

use App\Models\Notifications;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:commissions';

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
        Log::info("Entrando para notificar comisiones");
        try {

            $settingDayToSend = Setting::select("val")->where("key", "day_send_commissions")->first();
            Log::info($settingDayToSend);
            if ($settingDayToSend) {
                $day = (int) $settingDayToSend->val;
                $todayDate = now();
                $today = $todayDate->day;
                $monthBefore = date("Y-m-d", strtotime('now - 1 month + 1 day '));
                if ($day === $today) {

                    $commission = Commission::where("start_date", ">=", $monthBefore)
                        ->where("start_date", "<=", $todayDate)
                        ->with([
                            "contracts" => function ($q) {
                                $q->with(["worker", "company"]);
                            }
                        ])
                        ->get();
                    Log::info($monthBefore);
                    Log::info(json_encode($commission));
                    if (count($commission)) {

                        // Notifications::create([
                        //     "text" => "Notificacios de les commisions de treballadors de la empreses",
                        //     "type" => "email_commisions",
                        //     "to" => env("DEVELOPER_MAIL")
                        // ]);
                        $email = env("DEVELOPER_MAIL");

                        //Agrumamos comisiones
                        $com = [];
                        foreach ($commission as $c) {
                            Log::debug($c);
                            $com[$c->contracts->company->name_with_cif][$c->contracts->worker->full_name_with_dni][] = [
                                "type" => $c->type,
                                "import" => $c->import,
                                "start_date" => $c->start_date,
                                "observation" => $c->observation,
                            ];
                        }

                        Log::debug(json_encode($com));
                        Mail::to($email)
                            ->send(new CommissionsNotifications(
                                $com
                            ));

                        if (Mail::failures()) {
                            //$commission->notificated_iban_attemps += 1;
                        } else {
                            //$commission->notificated_at = now();
                        }
                        //$commission->save();
                    } else {
                        Log::info('No comisions to notificate');
                    }
                } else {
                    Log::info('No es dia de notificar');
                }
            }
        } catch (\Exception $exception) {
            Log::info("error notificando comisiones: " . $exception->getMessage());
        }
    }
}
