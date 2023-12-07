<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\WorkCenter;
use Illuminate\Support\Facades\Log;

class UpdateWorkCentersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:work-centers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update work centers with company locations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::whereNotNull('location')->get();
        Log::info('mensaje',['data'=>$companies ]);
        foreach ($companies as $company) {
            $workCenter = new WorkCenter();
            $workCenter->company_id = $company->id;
            $workCenter->name = $company->location;
            $workCenter->save();
        }

        $this->info('Work centers synchronized successfully.');
    }
}
