<?php

namespace App\Console\Commands;

use App\Media;
use App\Youtube\YoutubeChannelVideos;
use Illuminate\Console\Command;

class YoutubeVideosCommand extends Command
{
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
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('===========================================');
        $this->info("Getting videos available on channel {$this->argument('channelId')}");
        $this->info('===========================================');
        $factory = YoutubeChannelVideos::forChannel($this->argument('channelId'));

        if ($this->option('raw')) {
            print_r($factory->videos());
            return 0;
        }

        array_map(function ($item) {
            $this->line("{$item['media_id']} - {$item['published_at']->format('d/m/Y H:i')} - {$item['title']}  ");
        }, $factory->videos());

        $this->info('Url : ' . Media::YoutubeUrl($this->argument('channelId')));
        return 0;
    }
}
