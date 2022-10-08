<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Media;
use App\Youtube\YoutubeChannelVideos;
use Illuminate\Console\Command;

class YoutubeVideosCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:videos {channelId} {--raw}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the videos from a youtube channel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->prologue();

        $this->info('===========================================');
        $this->info("Getting videos available on channel {$this->argument('channelId')}");
        $this->info('===========================================');
        $factory = YoutubeChannelVideos::forChannel($this->argument('channelId'));

        if ($this->option('raw')) {
            print_r($factory->videos());

            return 0;
        }

        array_map(function ($item): void {
            $this->line("{$item['media_id']} - {$item['published_at']->format('d/m/Y H:i')} - {$item['title']}  ");
        }, $factory->videos());

        $this->info('Url : ' . Media::YoutubeUrl($this->argument('channelId')));

        $this->epilogue();

        return Command::SUCCESS;
    }
}
