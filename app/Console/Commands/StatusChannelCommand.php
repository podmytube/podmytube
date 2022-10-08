<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Exceptions\UnknownChannelException;
use App\Exceptions\YoutubeChannelHasNoPublicMediaException;
use App\Models\Channel;
use App\Models\Media;
use App\Youtube\YoutubeChannelVideos;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StatusChannelCommand extends Command
{
    use BaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:channel {channel_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get information on channel and medias (grabbed and not).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->prologue();

        try {
            $channel = Channel::byChannelId($this->argument('channel_id'));

            // no channel to refresh => nothing to do
            if ($channel === null) {
                $message = "There is no channel with this channel_id ({$this->argument('channel_id')})";

                throw new UnknownChannelException($message);
            }

            $factory = YoutubeChannelVideos::forChannel($channel->channel_id, 50);
            $nbVideos = count($factory->videos());
            if ($nbVideos <= 0) {
                $message = "This channel ({$this->argument('channel_id')}) seems to have no videos.";

                throw new YoutubeChannelHasNoPublicMediaException($message);
            }

            // for each channel video
            $outputTable = [];
            array_map(function ($video) use (&$outputTable): void {
                $media = Media::byMediaId($video['media_id']);
                if ($media === null) {
                    return;
                }

                $outputTable[] = [
                    'media_id' => $media->media_id,
                    'title' => $media->title,
                    'published_at' => $media->published_at?->toDateString() ?? '-',
                    'grabbed' => $media->hasBeenGrabbed() ? '✅' : '-',
                ];
            }, $factory->videos());

            $this->table(
                ['Channel ID', 'Channel name', 'Email', 'Created', 'Updated', 'Subscription', 'Active'],
                [[
                    $channel->youtube_id,
                    $channel->channel_name,
                    $channel->user->email,
                    $channel->channel_createdAt->toDateString(),
                    $channel->podcast_updatedAt?->toDateString() ?? '-',
                    $channel->subscription->plan->name,
                    $channel->isActive() ? '✅' : '❌',
                ]]
            );
            $this->line('');
            $this->table(
                ['Media ID', 'Title', 'Published at', 'Grabbed'],
                $outputTable
            );

            $errCode = 0;
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
            Log::error($exception->getMessage());
            $errCode = 1;
        }

        $this->epilogue();

        return $errCode;
    }
}
