<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CleanFreeChannelMediasCommand;
use App\Console\Commands\DownloadVideosByPeriodCommand;
use App\Console\Commands\GetPlaylistMediasCommand;
use App\Console\Commands\GetPlaylistsCommand;
use App\Console\Commands\LastMediaPublishedChecker;
use App\Console\Commands\ProcessLogsCommand;
use App\Console\Commands\SendMonthlyReportsCommand;
use App\Console\Commands\UpdateBlogPostsCommand;
use App\Console\Commands\UpdateChannelsCommand;
use App\Console\Commands\UpdatePlaylistsForPayingChannelsCommand;
use App\Console\Commands\UpdateSitemapCommand;
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
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // refresh media list from youtube api for channels
        $schedule->command(UpdateChannelsCommand::class, ['all'])->hourlyAt(1);

        // refresh media list from youtube api for playlists
        $schedule->command(GetPlaylistMediasCommand::class)->hourlyAt(6);

        // grabbing non grabbed videos
        $schedule->command(DownloadVideosByPeriodCommand::class)->hourlyAt(12);

        // get playlists from active channels
        $schedule->command(GetPlaylistsCommand::class)->hourlyAt(35);

        // build playlists feeds
        $schedule->command(UpdatePlaylistsForPayingChannelsCommand::class)->hourlyAt(45);

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */
        $schedule->command(ProcessLogsCommand::class)->hourlyAt(55);

        /*
        |--------------------------------------------------------------------------
        | Specials
        |--------------------------------------------------------------------------
        */
        // generating sitemap
        $schedule->command(UpdateSitemapCommand::class)->daily();

        // Check media
        $schedule->command(LastMediaPublishedChecker::class)->everySixHours();

        // Check blog post
        $schedule->command(UpdateBlogPostsCommand::class)->dailyAt('23h27');

        /*
        |--------------------------------------------------------------------------
        | Monthly
        |--------------------------------------------------------------------------
        */
        // cleaning free medias old episodes - 12h
        $schedule->command(CleanFreeChannelMediasCommand::class)->monthlyOn($day = 1, $time = '12:0');

        // monthly report on first monday
        $schedule->command(SendMonthlyReportsCommand::class)->monthlyOn($day = 1, $time = '11:0');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): string
    {
        return 'Europe/Paris';
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
