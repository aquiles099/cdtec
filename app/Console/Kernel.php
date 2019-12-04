<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CloneFarmsAccountsNodes;
use App\Console\Commands\CloneZones;
use App\Console\Commands\ClonePumpsystems;
use App\Console\Commands\CloneHydraulics;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CloneFarmsAccountsNodes::class,
        CloneZones::class,
        ClonePumpsystems::class,
        CloneHydraulics::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('clone:farms:accounts:nodes:run')->hourly();
        // $schedule->command('clone:zones:run')->hourly();
        // $schedule->command('clone:pumpsystems:run')->hourly();
        // $schedule->command('clone:hydraulics:run')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
