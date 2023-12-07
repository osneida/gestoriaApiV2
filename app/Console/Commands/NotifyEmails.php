<?php

namespace App\Console\Commands;

use App\Mail\NoMailNotification;
use App\Models\Company;
use App\Models\Notifications;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:not-email';

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
        Log::info("Entrando para notificar emails");
        try {

            $settingDayToSend = Setting::select("val")->where("key", "day_send_email")->first();
            Log::info("setting",['data'=>$settingDayToSend]);
            if ($settingDayToSend->exists) {
                $day = (int) $settingDayToSend->val;
                $today = now()->day;
                if ($day === $today) {

                    $company = Company::whereHas("workers", function ($q) {
                        $q->whereNull("email");
                    })
                        ->with([
                            "workers" => function ($q) {
                                $q->whereNull("email");
                            }
                        ])
                        ->where(function ($q) {
                            $q->whereDate("not_email", "<>", now())
                                ->orWhereNull("not_email");
                        })

                        ->first();
                    //dd($company);
                    if ($company) {
                        Log::info(json_encode($company));
                        // Notifications::create([
                        //     "text" => "Notificacios de falta de email de treballadors de la empresa $company->name",
                        //     "type" => "email_no_email",
                        //     "to" => "kevin.tucody@gmail.com"
                        // ]);
                        Mail::to("kevin.tucody@gmail.com")
                            ->send(new NoMailNotification(
                                $company,
                                $company->workers
                            ));


                        if (Mail::failures()) {
                            $company->not_email_attemps += 1;
                        } else {
                            $company->not_email = now();
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
