<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Worker;
use App\Models\WorkCenter;

class SyncWorkersWorkCenter extends Command
{
    protected $signature = 'sync:workers-work-center';

    protected $description = 'Syncs workers to their corresponding work centers';

    public function handle()
    {
        $companyWorkers = DB::table('company_worker')->get();

        foreach ($companyWorkers as $companyWorker) {
            $worker = Worker::find($companyWorker->worker_id);
            if ($worker) {
                $workCenter = WorkCenter::where('company_id', $companyWorker->company_id)->first();
                if ($workCenter) {
                    $worker->work_center_id = $workCenter->id;
                    $worker->save();
                }
            }
        }

        $this->info('Workers synchronized to work centers successfully.');
    }
}
