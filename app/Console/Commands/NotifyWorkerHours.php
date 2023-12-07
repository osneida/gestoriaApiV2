<?php

namespace App\Console\Commands;

use App\Exports\WorkerHoursExport;
use App\Mail\WorkerHoursMail;
use App\Models\Company;
use App\Models\Setting;
use App\Models\WorkerHours;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use \PDF;

class NotifyWorkerHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:worker-hours';

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

    public static $DAY_SEND_KEY = 'day_send_worker_hours';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        DB::enableQueryLog();

        $settingDayToSend = Setting::select("val")->where("key", self::$DAY_SEND_KEY)->first();
        if ($settingDayToSend) {
            $day = (int) $settingDayToSend->val;
            $todayDate = now();
            $today = $todayDate->day;
            if ($day === $today || True) {
                $year = date("Y", strtotime('now - 1 month '));
                $month = date("m", strtotime('now - 1 month '));
                $start_month = date("Y-m-d", strtotime("$year-$month-01"));
                $end_month = date("Y-m-t", strtotime("$year-$month-01"));


                $cs = Company::where('has_worker_hors', 1)->with(["workers" => function ($q) use ($start_month, $end_month) {
                    $q->whereHas('contracts', function ($q) use ($start_month, $end_month) {
                        $q->whereDate("contract_start_date", "<=", $end_month);
                        $q->where(function ($q) use ($start_month) {
                            $q->whereDate("contract_end_date", ">=", $start_month);
                            $q->orWhereNull("contract_end_date");
                        });
                    });
                }])->get();
                $attachments = [];
                foreach ($cs as $c) {
                    $hs = WorkerHours::whereHas('contract', function ($q) use ($c) {
                        $q->where("company_id", $c->id);
                    })
                        ->whereMonth("date", $month)
                        ->whereYear("date", $year)
                        ->get();


                    Log::debug("$c->id - $c->name_with_cif [$month - $year]");

                    $tableHeader = [""];

                    $table =  [];
                    $tableFooter = [""];
                    for ($day = strtotime($start_month); $day <= strtotime($end_month); $day = strtotime(date("Y-m-d", $day) . " +1 day")) {
                        $line = [date('d-m-Y', $day)];
                        foreach ($c->workers as $key => $w) {
                            if (count($tableHeader) <= count($c->workers)) {
                                $tableHeader[] = $w->full_name_with_dni;
                                $tableFooter[] = 0;
                            }

                            $wh = WorkerHours::whereHas('contract', function ($q) use ($c, $w) {
                                $q->where("company_id", $c->id);
                                $q->where("worker_id", $w->id);
                            })
                                ->whereDate("date", date('Y-m-d', $day))
                                ->first();

                            if ($wh) {
                                $line[] = $wh->hours;
                                $tableFooter[$key + 1] += $wh->hours;
                            } else $line[] = "-";
                        }
                        $table[] = $line;
                    }


                    $tableComplete = [
                        "header" => $tableHeader,
                        "body" => $table,
                        "footer" => $tableFooter
                    ];
                    $tableComplete = array_merge([$tableHeader], $table, [$tableFooter]);

                    // $pdf = PDF::loadView("pdf.worker_hours", compact("tableComplete"));
                    //$pdf->setPaper('A4', 'landscape');

                    //$pdf->save(public_path("worker_hours.pdf"));


                    $excel = new WorkerHoursExport($tableComplete);

                    $ex = Excel::raw($excel, \Maatwebsite\Excel\Excel::XLSX);

                    $attachments[] = ["name" => $c->name, "file" => $ex];
                    //Log::debug($attachments);
                }
                //Excel::store($excel, "worker_hours.xlsx");
            }

            Mail::to(env("DEVELOPER_MAIL"))->send(new WorkerHoursMail($attachments));
        }
    }
}
