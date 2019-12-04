<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CloneByFarmFarmsAccountsNodes;
use App\Console\Commands\CloneByFarmZones;
use App\Console\Commands\CloneByFarmPumpsystems;
use App\Console\Commands\CloneByFarmHydraulics;
use App\Console\Commands\CloneByFarmMeasures;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CloneByFarmFarmsAccountsNodes::class,
        CloneByFarmZones::class,
        CloneByFarmPumpsystems::class,
        CloneByFarmHydraulics::class,
        CloneByFarmMeasures::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // php artisan clonebyfarm:farms:accounts:nodes:run
        // $schedule->command('clonebyfarm:farms:accounts:nodes:run')->hourly();
        // php artisan clonebyfarm:zones:run
        // $schedule->command('clonebyfarm:zones:run')->hourly();
        // php artisan clonebyfarm:pumpsystems:run
        // $schedule->command('clonebyfarm:pumpsystems:run')->hourly();
        // php artisan clonebyfarm:hydraulics:run
        // $schedule->command('clonebyfarm:hydraulics:run')->hourly();
        // php artisan clonebyfarm:measures:run
        // $schedule->command('clonebyfarm:measures:run')->hourly();
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
