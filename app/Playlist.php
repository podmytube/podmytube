<?php
/**
 * the playlist model to access database same table name
 *
 * Mainly redefine the primary key and the relationship between one channel and its playlist
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use App\Exceptions\PlaylistWithNoMediaWeKnowAboutException;
use App\Exceptions\PlaylistWithNoVideosException;
use App\Interfaces\Podcastable;
use App\Podcast\PodcastItem;
use App\Traits\BelongsToChannel;
use App\Youtube\YoutubePlaylistItems;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

/**
 * the Playlist class and its functions
 */
class Playlist extends Model implements Podcastable
{
    use BelongsToChannel;

    protected $guarded = [];

    public function title():string
    {
        return $this->title;
    }

    public function link():?string
    {
        return $this->channel->link;
    }

    public function description():?string
    {
        return $this->description;
    }

    public function authors():?string
    {
        return $this->channel->authors;
    }

    public function email():?string
    {
        return $this->channel->email;
    }

    public function copyright():?string
    {
        return $this->channel->podcast_copyright;
    }

    public function languageCode():?string
    {
        return optional($this->language)->code;
    }

    public function category():?Category
    {
        return $this->channel->category;
    }

    public function explicit():?bool
    {
        return $this->channel->explict;
    }

    public function mediasToPublish():Collection
    {
        /**
         * get all items from youtube playlist
         */
        $videos = (new YoutubePlaylistItems())->forPlaylist($this->youtube_playlist_id)->videos();

        if (!count($videos)) {
            throw new PlaylistWithNoVideosException("This playlist {$this->youtube_playlist_id} has no video.");
        }

        $mediaIds = array_map(function ($video) {
            return $video['media_id'];
        }, $videos);

        /**
         * get the ones that I know about
         */
        $medias = Media::grabbedAt()->whereIn('media_id', $mediaIds)->get();

        if (!$medias->count()) {
            throw new PlaylistWithNoMediaWeKnowAboutException("This playlist {$this->youtube_playlist_id} has no video we know about.");
        }

        return $medias;
    }

    public function podcastItems():SupportCollection
    {
        return $this->mediasToPublish()
            ->map(function (Media $media) {
                return PodcastItem::with($media->toPodcastItem());
            });
    }

    public function podcastCoverUrl():string
    {
        return $this->channel->podcastCoverUrl();
    }

    /**
     * return informations needed to generate podcast header.
     */
    public function podcastHeader():array
    {
        return  [
            'title' => $this->title(),
            'link' => $this->link(),
            'description' => $this->description(),
            'authors' => $this->authors(),
            'email' => $this->email(),
            'copyright' => $this->copyright(),
            'imageUrl' => $this->podcastCoverUrl(),
            'language' => $this->languageCode(),
            'category' => $this->category(),
            'explicit' => $this->explicit(),
        ];
    }

    public function toPodcast():array
    {
        return array_merge(
            $this->podcastHeader(),
            ['podcastItems' => $this->podcastItems()]
        );
    }
}
