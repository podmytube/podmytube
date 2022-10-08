<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Modules\ServerRole;
use App\Youtube\YoutubePlaylistItems;
use Carbon\Carbon;
use Illuminate\Console\Command;

class YoutubePlaylistItemsCommand extends Command
{
    use BaseCommand;

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
     */
    public function handle(): int
    {
        if (!ServerRole::isWorker()) {
            $this->info('This server is not a worker.', 'v');

            return 0;
        }

        $this->prologue();
        $this->info('===========================================');
        $this->info("Getting playlist items for {$this->argument('playlistId')}");
        $this->info("options : limit {$this->option('limit')}");
        $this->info('===========================================');
        $limit = (int) $this->option('limit');

        $factory = YoutubePlaylistItems::init()
            ->setLimit($limit)
            ->forPlaylist($this->argument('playlistId'))
        ;

        if ($this->option('raw')) {
            print_r($factory->items());

            return 0;
        }

        array_map(function ($item): void {
            $publishedAt = Carbon::parse($item['contentDetails']['videoPublishedAt'])->setTimezone('UTC')->format('d/m/Y H:i');
            $this->line("{$item['contentDetails']['videoId']} - {$publishedAt} - {$item['snippet']['title']}");
        }, $factory->items());

        $this->epilogue();

        return Command::SUCCESS;
    }
}
