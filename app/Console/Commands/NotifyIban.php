<?php

namespace App\Console\Commands;

use App\Mail\NoIbanNotification;
use App\Mail\NoMailNotification;
use App\Models\Company;
use App\Models\Notifications;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyIban extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:not-iban';

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
        Log::info("Entrando para notificar ibans");
        try {

            $settingDayToSend = Setting::select("val")->where("key", "day_send_iban")->first();

            if ($settingDayToSend->exists) {
                $day = (int) $settingDayToSend->val;
                $today = now()->day;
                if ($day === $today) {

                    $company = Company::whereHas("workers", function ($q) {
                        $q->with(["contracts" => function ($q) {
                            $q->whereDate("contract_end_date", ">=", now())->whereDate("contract_start_date", "<=", now());
                        }]);
                    })
                        ->with([
                            "workers" => function ($q) {
                                $q->with(["contracts" => function ($q) {
                                    $q->whereDate("contract_end_date", ">=", now())->whereDate("contract_start_date", "<=", now());
                                }]);
                            }
                        ])
                        ->where(function ($q) {
                            $q->whereDate("not_iban", "<>", now())
                                ->orWhereNull("not_iban");
                        })
                        ->first();
                    if ($company) {
                        Log::info(json_encode($company));
                        Notifications::create([
                            "text" => "Notificacios de falta de IBAN de treballadors de la empresa $company->name",
                            "type" => "email_no_iban",
                            "to" => "pau@tucody.com"
                        ]);
                        Mail::to("pau@tucody.com")
                            ->send(new NoIbanNotification(
                                $company,
                                $company->workers
                            ));


                        if (Mail::failures()) {
                            $company->not_iban_attemps += 1;
                        } else {
                            $company->not_iban = now();
                        }
                        $company->save();
                    } else {
                        Log::info('No companie to notificate');
                    }
                } else {
                    Log::info('No es dia de notificar');
                }
            }
        } catch (\Exception $exception) {
            Log::info("error procesando nóminas: " . $exception->getMessage());
        }
    }
}
