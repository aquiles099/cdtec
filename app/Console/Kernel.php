<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CloneByFarmFarmsAccountsNodes;
use App\Console\Commands\CloneByFarmZones;
use App\Console\Commands\CloneByFarmPumpsystems;
use App\Console\Commands\CloneByFarmHydraulics;
use App\Console\Commands\CloneByFarmMeasures;
use App\Console\Commands\CloneByFarmIrrigationsVolumes;
use App\Console\Commands\CloneByFarmRealIrrigationsVolumes;
use App\Console\Commands\CloneByFarmAlarms;
use App\Console\Commands\CloneByZonePumpsystems;
use App\Console\Commands\CloneByZoneMeasures;
use App\Console\Commands\CloneByZoneIrrigationsVolumes;
use App\Console\Commands\CloneByZoneRealIrrigationVolumes;
use App\Console\Commands\CloneByZoneAlarms;
use App\Console\Commands\CloneByNodeMeasures;
use App\Console\Commands\CloneByIrrigationRealIrrigations;
use App\Console\Commands\CloneByPumpsystemIrrigationsVolumes;
use App\Console\Commands\CloneByPumpsystemRealIrrigationsVolumes;
use App\Console\Commands\CloneByPumpsystemZones;
use App\Console\Commands\CloneByMeasureData;
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
        CloneByFarmMeasures::class,
        CloneByFarmIrrigationsVolumes::class,
        CloneByFarmRealIrrigationsVolumes::class,
        //CloneByFarmAlarms::class, retorna error: "Error to list of Alarms"
        CloneByZonePumpsystems::class,
        CloneByZoneMeasures::class,
        CloneByZoneIrrigationsVolumes::class,
        CloneByZoneRealIrrigationVolumes::class,
        //CloneByZoneAlarms::class, retorna error: "Error to list of Alarms"
        //CloneByNodeMeasures::class, retorna error: {"message":"User is not authorized to access this resource with an explicit deny"}
        CloneByIrrigationRealIrrigations::class,
        CloneByPumpsystemIrrigationsVolumes::class,
        CloneByPumpsystemRealIrrigationsVolumes::class,
        CloneByPumpsystemZones::class,
        CloneByMeasureData::class
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
        $schedule->command('clonebyfarm:farms:accounts:nodes:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:zones:run
        $schedule->command('clonebyfarm:zones:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:pumpsystems:run
        $schedule->command('clonebyfarm:pumpsystems:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:hydraulics:run
        $schedule->command('clonebyfarm:hydraulics:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:measures:run
        $schedule->command('clonebyfarm:measures:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:irrigations:volumes:run
        $schedule->command('clonebyfarm:irrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:realirrigations:volumes:run
        $schedule->command('clonebyfarm:realirrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebyfarm:alarms:run
        // $schedule->command('clonebyfarm:alarms:run')->everyFifteenMinutes();
        // php artisan clonebyzone:pumpsystems:run
        $schedule->command('clonebyzone:pumpsystems:run')->everyFifteenMinutes();
        // php artisan clonebyzone:measures:run
        $schedule->command('clonebyzone:measures:run')->everyFifteenMinutes();
        // php artisan clonebyzone:irrigations:volumes:run
        $schedule->command('clonebyzone:irrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebyzone:realirrigations:volumes:run
        $schedule->command('clonebyzone:realirrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebyzone:alarms:run
        // $schedule->command('clonebyzone:alarms:run')->everyFifteenMinutes();
        // php artisan clonebynode:measures:run
        // $schedule->command('clonebynode:measures:run')->everyFifteenMinutes();
        // php artisan clonebyirrigation:realirrigations:run
        $schedule->command('clonebyirrigation:realirrigations:run')->everyFifteenMinutes();
        // php artisan clonebypumpsystem:irrigations:volumes:run
        $schedule->command('clonebypumpsystem:irrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebypumpsystem:realirrigations:volumes:run
        $schedule->command('clonebypumpsystem:realirrigations:volumes:run')->everyFifteenMinutes();
        // php artisan clonebypumpsystem:zones:run
        $schedule->command('clonebypumpsystem:zones:run')->everyFifteenMinutes();
        // php artisan clonebymeasure:data:run
        $schedule->command('clonebymeasure:data:run')->everyFifteenMinutes();
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
