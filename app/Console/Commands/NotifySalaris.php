<?php

namespace App\Console\Commands;

use App\Mail\SalariesNotification;
use App\Models\Notifications;
use App\Models\Salary;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifySalaris extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:salary';

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
        Log::info("Entrando para notificar salarios");
        try {

            $settingDayToSend = Setting::select("val")->where("key", "day_send_salary")->first();
            Log::info($settingDayToSend);
            if ($settingDayToSend) {
                $day = (int) $settingDayToSend->val;
                $todayDate = now();
                $today = $todayDate->day;
                $monthBefore = date("Y-m-d", strtotime('now - 1 month + 1 day '));
                Log::info('salaryy',['today'=>$today,'monthBefore'=>$monthBefore]);
                if ($day === $today) {

                    $salaries = Salary::where("created_at", ">=", $monthBefore)
                        ->where("created_at", "<=", $todayDate)
                        ->with([
                            "contracts" => function ($q) {
                                $q->with(["worker", "company"]);
                            }
                        ])
                        ->get();
                    Log::info($monthBefore);
                    Log::info(json_encode($salaries));
                    if (count($salaries)) {

                        // Notifications::create([
                        //     "text" => "Notificacios de salarys de treballadors de la empreses",
                        //     "type" => "email_salary",
                        //     "to" => env("DEVELOPER_MAIL")
                        // ]);
                        $email = env("DEVELOPER_MAIL");

                        //Agrumamos comisiones
                        $sal = [];
                        foreach ($salaries as $s) {
                            Log::debug($s);
                            $sal[$s->contracts->company->name_with_cif][$s->contracts->worker->full_name_with_dni][] = [
                                "salary" => $s->salary,
                                "start_date" => $s->start_date,
                            ];
                        }

                        Log::debug(json_encode($sal));
                        Mail::to($email)
                            ->send(new SalariesNotification(
                                $sal
                            ));

                        if (Mail::failures()) {
                            //$salaries->notificated_iban_attemps += 1;
                        } else {
                            //$salaries->notificated_at = now();
                        }
                        //$salaries->save();
                    } else {
                        Log::info('No salaries to notificate');
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
