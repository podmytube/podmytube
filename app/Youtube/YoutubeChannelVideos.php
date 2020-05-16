<?php

namespace App\Youtube;

class YoutubeChannelVideos extends YoutubeCore
{
    /** @var string $channelId $youtube channel id */
    protected $channelId;
    /** @var string $uploadsPlaylistId $youtube playlist id */
    protected $uploadsPlaylistId;

    public function channel(string $channelId)
    {
        $this->channelId = $channelId;
        /**
         * get the uploads playlist id
         */

        /**
         * get all the uploaded videos for that playlist
         */
        return $this;
    }

    /**
     * obtain the 'uploads' playlist id from youtube.
     *
     * @return App\Youtube\YoutubeChannelVideos
     */
    protected function getUploadsPlaylistIdFromYoutube()
    {
        $items = $this->defineEndpoint('channels.list')
            ->addParts(['id', 'contentDetails'])
            ->addParams(['id' => $this->channelId])
            ->run()
            ->items();

        $this->uploadsPlaylistId =
            $items[0]['contentDetails']['relatedPlaylists']['uploads'];

        return $this;
    }

    /**
     * return the 'uploads' playlist id.
     * if already set, returns it. else will ask youtube api.
     *
     * @return string 'uploads' playlist id
     */
    public function uploadPlaylistId()
    {
        if ($this->uploadsPlaylistId === null) {
            $this->getUploadsPlaylistIdFromYoutube();
        }
        return $this->uploadsPlaylistId;
    }

    public function videos()
    {
        dump(
            $this->defineEndpoint('playlistItems.list')
                ->addParts(['id', 'contentDetails'])
                ->addParams(['playlistId,' => $this->uploadsPlaylistId])
                ->run()
                ->items()
        );
    }
}
