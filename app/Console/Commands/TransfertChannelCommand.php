<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\BaseCommand;
use App\Models\Category;
use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class TransfertChannelCommand extends Command
{
    use BaseCommand;

    protected $signature = 'transfert:channel {from_channel_id} {dest_channel_id} {--user_id} {--plan_id} {--copy}';
    protected $description = 'transfer one channel';

    protected ?Channel $fromChannel;
    protected ?Channel $destChannel;

    public function handle()
    {
        $this->prologue();
        // obtain channels from DB
        $this->fromChannel = Channel::byChannelId($this->argument('from_channel_id'));
        $this->destChannel = Channel::byChannelId($this->argument('dest_channel_id'));

        throw_if($this->fromChannel === null, new InvalidArgumentException("Channel {$this->argument('from_channel_id')} does not exist."));

        $destUser = $this->getUser();

        $plan = $this->getPlan();

        if ($this->destChannel === null) {
            // destChannel does not exists => create it
            $this->destChannel = $this->createDestinationChannel(
                user: $destUser,
                plan: $plan,
                channelId: $this->argument('dest_channel_id')
            );
            throw_if(
                $this->destChannel === null,
                new InvalidArgumentException("Channel {$this->argument('dest_channel_id')} creation has failed.")
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Thumbs/Vignettes
        |--------------------------------------------------------------------------
        */
        if ($this->fromChannel->hasCover() && !$this->destChannel->hasCover()) {
            // create thumb copy
            $newThumb = $this->fromChannel->cover->replicate([
                'coverable_type',
                'coverable_id',
            ]);

            // attach it to destChannel
            $this->destChannel->cover()->save($newThumb);
            $this->destChannel->refresh();

            if ($this->option('copy')) {
                // copy thumb file
                Storage::disk('remote')->copy(
                    from: $this->fromChannel->cover->remoteFilePath(),
                    to: $this->destChannel->cover->remoteFilePath(),
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Medias
        |--------------------------------------------------------------------------
        */
        if ($this->fromChannel->medias->count()) {
            if ($this->option('copy')) {
                $fromChannelMediasFolder = config('app.mp3_path') . $this->fromChannel->channel_id;
                $destChannelMediasFolder = config('app.mp3_path') . $this->destChannel->channel_id;
                // copy media files
                array_map(
                    fn (string $filePath) => Storage::disk('remote')->copy(
                        from: $filePath,
                        to: $destChannelMediasFolder . '/' . basename($filePath),
                    ),
                    Storage::disk('remote')->files($fromChannelMediasFolder)
                );
            }

            // update all medias (cannot copy, media_id is unique)
            Media::query()
                ->where('channel_id', '=', $this->fromChannel->channel_id)
                ->update([
                    'channel_id' => $this->destChannel->channel_id,
                ])
            ;
        }

        $this->epilogue();

        return 0;
    }

    protected function createDestinationChannel(User $user, Plan $plan, string $channelId): Channel
    {
        // Creating channel
        $channel = Channel::create([
            'user_id' => $user->id,
            'channel_id' => $channelId,
            'category_id' => Category::bySlug(Channel::DEFAULT_CATEGORY_SLUG)->id,
            'active' => true,
        ]);

        // adding subscription
        Subscription::query()
            ->updateOrCreate(
                ['channel_id' => $channel->channelId()],
                [
                    'channel_id' => $channel->channelId(),
                    'plan_id' => $plan->id,
                ]
            )
        ;

        return $channel;
    }

    protected function getUser(): User
    {
        $destUser = $this->fromChannel->user;
        if ($this->option('user_id') !== false) {
            $destUser = User::find($this->option('user_id'));
            throw_if($destUser === null, new InvalidArgumentException("This user id {$this->option('user_id')} is unknown in database."));
        }

        return $destUser;
    }

    protected function getPlan(): Plan
    {
        $plan = $this->fromChannel->plan;
        if ($this->option('plan_id') !== false) {
            $plan = Plan::find($this->option('plan_id'));
            throw_if($plan === null, new InvalidArgumentException("This plan id {$this->option('plan_id')} is unknown in database."));
        }

        return $plan;
    }
}
