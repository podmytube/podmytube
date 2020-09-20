<?php

namespace App\Console;

use App\Console\Commands\BatchPodcasts;
use App\Console\Commands\ChannelUpdateCommand;
use App\Console\Commands\CleanFreeChannelMedias;
use App\Console\Commands\LastMediaPublishedChecker;
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
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** cleaning free medias old episodes */
        $schedule->command(CleanFreeChannelMedias::class)->monthlyOn($day = 1, $time = '12:0');

        /** updating channels */
        $schedule->command(ChannelUpdateCommand::class, ['all'])->hourlyAt('2');

        /** Check media */
        $schedule->command(LastMediaPublishedChecker::class)->everySixHours('2');

        /** Building podcasts */
        $schedule->command(BatchPodcasts::class, ['all'])->hourlyAt('50');

        /** monthly report on first monday */
        $schedule->command(SendMonthlyReports::class)->monthly()->days([1])->at('11:00');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return string
     */
    protected function scheduleTimezone(): string
    {
        return 'Europe/Paris';
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
