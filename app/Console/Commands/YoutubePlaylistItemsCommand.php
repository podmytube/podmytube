<?php

namespace App\Console\Commands;

use App\Youtube\YoutubePlaylistItems;
use Carbon\Carbon;
use Illuminate\Console\Command;

class YoutubePlaylistItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:playlistitems {playlistId} {--limit=0} {--raw}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the playlist items for specified playlist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('===========================================');
        $this->info("Getting playlist items for {$this->argument('playlistId')}");
        $this->info("options : limit {$this->option('limit')}");
        $this->info('===========================================');
        $factory = YoutubePlaylistItems::init()
            ->setLimit($this->option('limit'))
            ->forPlaylist($this->argument('playlistId'));

        if ($this->option('raw')) {
            print_r($factory->items());
            return 0;
        }

        array_map(function ($item) {
            $publishedAt = Carbon::parse($item['contentDetails']['videoPublishedAt'])->setTimezone('UTC')->format('d/m/Y H:i');
            $this->line("{$item['contentDetails']['videoId']} - {$publishedAt} - {$item['snippet']['title']}");
        }, $factory->items());

        return 0;
    }
}
