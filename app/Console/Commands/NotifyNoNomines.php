<?php

namespace App\Console\Commands;

use App\Mail\NoPayrollNotification;
use App\Models\Company;
use App\Models\Notifications;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyNoNomines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:no-payrolls';

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

        try {
            $settingDayToSend = Setting::select("val")->where("key", "day_send_payrolls")->first();
            $period = sprintf('%s-%s', now()->year, now()->month);
            $lastMonthPeriod = date("Y-m", strtotime("now - 1 month"));

            if ($settingDayToSend->exists) {
                $day = (int) $settingDayToSend->val;
                $today = date("d", strtotime("now + 3 days"));
                if ($day == $today) {
                    $company = Company::whereDoesntHave("payrolPeriod", function ($q) use ($day, $period, $lastMonthPeriod) {
                        if ($day > 15) {
                            //Aquest mes
                            $q->where("period", $period);
                        } else {
                            //El mes pasat
                            $q->where("period", $lastMonthPeriod);
                        }
                    })->where(function ($q) {
                        $q->whereDate("not_payrolls", "<>", now())
                            ->orWhereNull("not_payrolls");
                    })
                        ->first();
                    if ($company) {
                        Log::info('Nomina de ' . $company->name);
                        $not = Notifications::create([
                            "text" => "Notificacios de falta de nòmines de treballadors de la empresa $company->name",
                            "type" => "email_no_nomines",
                            "to" => "pau@tucody.com"
                        ]);
                        Log::info(json_encode($not));

                        Mail::to(env("DEVELOPER_MAIL"))
                            ->send(new NoPayrollNotification(
                                $company,
                                $day > 15 ? $period : $lastMonthPeriod
                            ));
                        Log::info('POST MAIL');

                        if (Mail::failures()) {
                            Log::info('MAIL FAIL');
                            $company->not_payrolls_attemps += 1;
                        } else {
                            Log::info('MAIL GOOD');
                            $company->not_payrolls = now();
                        }
                        $company->save();
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::info("error procesando nóminas: " . $exception->getMessage());
        }
    }
}
