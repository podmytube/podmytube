<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Channel;
use App\Models\Media;
use App\Modules\PeriodsHelper;
use App\Youtube\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class YoutubeVideoTagsForChannelCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:tags {channelId} {--tag=podcast} {--period=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the video that are tagged with the specified tag (podcast by default) \
                        example : artisan youtube:tags UCMnHkvrh_1fMWTJA_ru9ATQ --tag=podcast --period=2021-01';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->prologue();

        $channel = Channel::byChannelId($this->argument('channelId'));
        if (!$channel) {
            $this->error("There is no registered channel with this id ({$this->argument('channelId')}).");

            return 1;
        }

        $periodHelper = PeriodsHelper::create();
        if ($this->option('period')) {
            $period = $this->option('period') ? Carbon::createFromFormat('Y-m', $this->option('period')) : Carbon::now();
            $periodHelper = PeriodsHelper::create($period->month, $period->year);
        }

        /** get youtube video for this channel */
        $medias = Media::where('channel_id', '=', $this->argument('channelId'))
            ->whereBetween('published_at', [$periodHelper->startDate(), $periodHelper->endDate()])
            ->orderBy('published_at', 'asc')
            ->get()
        ;

        if (!$medias->count()) {
            $this->error("There is no medias during this period for {$channel->nameWithId()}.");

            return 1;
        }

        $tagToLookFor = $this->option('tag');
        $results = $medias->map(function (Media $media) use ($tagToLookFor) {
            $videoFactory = YoutubeVideo::forMedia($media->media_id);

            return [
                'title' => $videoFactory->title(),
                'media_id' => $videoFactory->videoId(),
                'published' => $media->published_at->format('Y-m-d'),
                'tags' => implode(',', $videoFactory->tags()),
                'isTagged' => in_array($tagToLookFor, $videoFactory->tags()),
                'isGrabbed' => $media->isGrabbed(),
            ];
        });

        $this->line('');
        $this->comment("During this period here are the medias and tags published on {$channel->nameWithId()}");
        $results->map(
            function ($result) {
                $message = "* {$result['published']} - {$result['title']} - {$result['media_id']} (tags: {$result['tags']})";
                $message .= $result['isGrabbed'] ? ' - ???' : '';
                if ($result['isTagged']) {
                    $this->info($message);

                    return true;
                }
                $this->line($message);
            },
            $results
        );
        $this->line('');

        $this->epilogue();

        return Command::SUCCESS;
    }
}
