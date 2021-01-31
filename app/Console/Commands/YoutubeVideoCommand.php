<?php

namespace App\Console\Commands;

use App\Media;
use App\Youtube\YoutubeVideo;
use Illuminate\Console\Command;

class YoutubeVideoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:video {videoId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the details of a youtube video';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info("Getting details on video {$this->argument('videoId')}");
        $this->info('===========================================');
        $factory = YoutubeVideo::forMedia($this->argument('videoId'));

        $this->line("Title : {$factory->title()}");
        //$this->line("Description : {$factory->description()}");
        $this->line("Duration : {$factory->duration()}");

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
    }
}
