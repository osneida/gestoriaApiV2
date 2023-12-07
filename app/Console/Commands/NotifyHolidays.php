<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\NotificationsController;
use App\Mail\BaixaMedicaNotification;
use App\Mail\HolidaysNotification;
use App\Models\Holidays;
use App\Models\Notifications;
use App\Models\User;
use App\Models\WorkerFile;
use App\Notifications\holidays\Ask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:holidays';

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
        Log::info("Entrando para notificar vacaciones");
        try {
            $holidays = Holidays::whereNull("notificated_at")
                ->where("notification_attempts", '<', 5)
                ->with(['contract' => function ($query) {
                    $query->select('id', 'worker_id', 'company_id')->with(['worker.user', "company"]);
                }, 'approver'])
                ->inRandomOrder()
                ->first();
            if ($holidays) {
                Log::info(json_encode($holidays));

                $approved = $holidays->approved;
                $worker = $holidays->contract->worker->full_name_with_dni;
                $start_date = $approved ? $holidays->approved_start_date : $holidays->requested_start_date;
                $end_date = $approved ? $holidays->approved_end_date : $holidays->requested_end_date;
                $start_date = date("d/m/Y", strtotime($start_date));
                $end_date = date("d/m/Y", strtotime($end_date));
                $approver = $holidays->approver !== null ? $holidays->approver->name : null;

                if ($approved !== null && $holidays->approval_date == $holidays->updated_at) {
                    Log::info("NOTIFICAMOS TRABAJADOR");
                    $email = $holidays->contract->worker->email;
                    $user_id = $holidays->contract->worker->user->id;
                } else {
                    Log::info("NOTIFICAMOS COMPANY");

                    $company_user = User::whereIn('role_id', [1, 2])->where('company_id', $holidays->contract->company_id)->first();
                    if ($company_user) {
                        $email = $company_user->email;
                        $user_id = $company_user->id;
                    } else {
                        $email = "admin@ggmanagement.cat";
                    }
                    Log::info($email);

                    $aprover_id = $holidays->contract->worker->holiday_responsible ?? $holidays->contract->company->holiday_responsible;
                    if ($aprover_id) {
                        $u = User::where('id', $aprover_id)->first();
                        $email = $u->email;
                        $user_id = $u->id;
                    }
                }

                Log::info('sending mail to: ' . $email);
                Log::info('sending: ' . $user_id);

                if (config('app.debug')) {
                    //$email = "pau@tucody.com";
                }

                //Mail::to($email)->send(new HolidaysNotification($approved, $worker, $start_date, $end_date, $approver, $approved == false && $holidays->approval_date != $holidays->updated_at));
                NotificationsController::notificateUser($user_id, new Ask(
                    $approved,
                    $worker,
                    $start_date,
                    $end_date,
                    $approver,
                    $approved == false && $holidays->approval_date != $holidays->updated_at,
                    $holidays->contract->company->name_with_cif
                ));
                /*
                if (Mail::failures()) {
                    $holidays->notification_attempts++;
                } else {
                }
                */
                $holidays->notificated_at = now();

                $holidays->save();
            } else {
                Log::info('No holidays to notify');
            }
        } catch (\Exception $exception) {
            Log::info("error procesando vacaciones: " . $exception->getMessage());
        }
    }
}
