<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\ChannelAlreadyRegisteredException;
use App\Exceptions\ChannelCreationHasFailedException;
use App\Models\Category;
use App\Models\Channel;
use App\Modules\YoutubeChannelId;
use App\Youtube\YoutubeChannel;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateChannelFactory
{
    public const DEFAULT_CATEGORY_SLUG = 'society-culture';

    private function __construct()
    {
    }

    public static function fromYoutubeUrl(Authenticatable $user, string $youtubeUrl, bool $active = false): ?Channel
    {
        try {
            // extract channelId from url.
            $channelId = YoutubeChannelId::fromUrl($youtubeUrl)->get();

            // check if channel exists in youtube.
            $youtubeChannel = new YoutubeChannel();
            $youtubeChannel->forChannel($channelId);

            // check if channel has already been registered
            $channelExist = Channel::byChannelId($channelId);
            if ($channelExist !== null) {
                throw new ChannelAlreadyRegisteredException("This youtube channel ({$youtubeUrl}) has already been registered 🤔️.");
            }

            // Creating channel
            return Channel::create([
                'user_id' => $user->userId(),
                'channel_id' => $channelId,
                'channel_name' => $youtubeChannel->name(),
                'category_id' => Category::bySlug(self::DEFAULT_CATEGORY_SLUG)->id,
                'active' => $active,
            ]);
        } catch (ChannelAlreadyRegisteredException $thrownException) {
            throw $thrownException;
        } catch (Exception $thrownException) {
            $exception = new ChannelCreationHasFailedException();
            $exception->addInformations("error : {$thrownException->getMessage()}");

            throw $exception;
        }
    }
}
