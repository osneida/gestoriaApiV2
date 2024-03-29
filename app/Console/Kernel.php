<?php

namespace App\Console;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('gestoria:process-monthly-payrolls')->everyMinute();
        $schedule->command('gestoria:process-certificates')->everyMinute();
        //$schedule->command('gestoria:process-salary')->everyMinute();
        //$schedule->command('gestoria:notify-company-by-mail-before-contract-expires')->everyMinute();
        $schedule->command('mail:notify-contract')->everyMinute();
        $schedule->command('users:workers')->everyMinute();
        $schedule->command('mail:not-email')->everyMinute();
        $schedule->command("mail:worker-file")->everyMinute();
        $schedule->command('mail:not-iban')->everyMinute();
        $schedule->command('mail:holidays')->everyMinute();
        $schedule->command('workers:archive')->everyMinute();
        $schedule->command('telescope:prune')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
