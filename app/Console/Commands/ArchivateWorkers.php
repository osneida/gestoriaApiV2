<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ArchivateWorkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workers:archive';

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
            $w = Worker::where('archive', false)
                ->where('auto_archive', false)
                ->whereDoesntHave('contracts', function ($q) {
                    $q->whereDate('contract_end_date', ">=", now());
                    $q->orWhereNull('contract_end_date');
                })
                ->whereHas('contracts')
                ->inRandomOrder()
                ->first();
            Log::debug($w);
            if ($w) {

                $w->archive = true;
                $w->auto_archive = true;
                User::where("worker_id", $w->id)->update([
                    "password" => ""
                ]);
                $w->save();
                Log::debug("TRABAJADOR ARCHIVADO");
            } else {
                Log::debug("NO HAY TRABAJADORES PARA ARCHIVAR");
            }
        } catch (\Exception $exception) {
            //DB::rollback();
            Log::error("error archivando trabajadores: " . $exception->getMessage());
        }
    }
}
