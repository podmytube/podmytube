<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Media;
use App\Youtube\YoutubeVideo;
use Illuminate\Console\Command;

class YoutubeVideoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video {videoId} {--raw}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the details of a youtube video';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('===========================================');
        $this->info("Getting details on video {$this->argument('videoId')}");
        $this->info('===========================================');
        $factory = YoutubeVideo::forMedia($this->argument('videoId'));

        if ($this->option('raw')) {
            print_r($factory->item());

            return 0;
        }

        $this->line("Title : {$factory->title()}");
        // $this->line("Description : {$factory->description()}");
        $this->line("Duration : {$factory->duration()}");
        $this->line("Published : {$factory->publishedAtForHumans()} ({$factory->publishedAt()})");

        if ($factory->isAvailable()) {
            $this->info('This video is available !');
        } else {
            $this->error('This video is not available !');
        }

        if ($factory->isTagged()) {
            $this->comment('This video is tagged with ' . implode(',', $factory->tags()) . '.');
        } else {
            $this->comment('This video has no tag.');
        }
        $this->info('Url : ' . Media::YoutubeUrl($this->argument('videoId')));

        return 0;
    }
}
