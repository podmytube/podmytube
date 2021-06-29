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
use App\Interfaces\Coverable;
use App\Interfaces\Podcastable;
use App\Modules\Vignette;
use App\Podcast\PodcastItem;
use App\Traits\BelongsToChannel;
use App\Traits\HasCover;
use App\Youtube\YoutubePlaylistItems;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

/**
 * the Playlist class and its functions
 */
class Playlist extends Model implements Podcastable, Coverable
{
    use BelongsToChannel, HasCover;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function mediasToPublish(): Collection
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
        $medias = Media::grabbedAt()
            ->whereIn('media_id', $mediaIds)
            ->orderBy('published_at', 'desc')
            ->get();

        if (!$medias->count()) {
            throw new PlaylistWithNoMediaWeKnowAboutException(
                "This playlist {$this->youtube_playlist_id} has no video we know about."
            );
        }

        return $medias;
    }

    public function podcastItems(): SupportCollection
    {
        return $this->mediasToPublish()
            ->map(function (Media $media) {
                return PodcastItem::with($media->toPodcastItem());
            });
    }

    public function podcastCoverUrl(): string
    {
		if (!$this->hasCover()) {
            return Thumb::defaultUrl();
        }

        return $this->cover->podcastUrl();
    }

    /**
     * return informations needed to generate podcast header.
     */
    public function podcastHeader(): array
    {
        return [
            'title' => $this->podcastTitle(),
            'link' => $this->podcastLink(),
            'description' => $this->podcastDescription(),
            'author' => $this->podcastAuthor(),
            'email' => $this->podcastEmail(),
            'copyright' => $this->podcastCopyright(),
            'imageUrl' => $this->podcastCoverUrl(),
            'language' => $this->podcastLanguage(),
            'category' => $this->podcastCategory(),
            'explicit' => $this->podcastExplicit(),
        ];
    }

    public function toPodcast(): array
    {
        return array_merge(
            $this->podcastHeader(),
            ['podcastItems' => $this->podcastItems()]
        );
    }

    public function podcastTitle(): string
    {
        return $this->title;
    }

    public function podcastLink(): ?string
    {
        return $this->channel->podcastLink();
    }

    public function podcastDescription(): ?string
    {
        return $this->description;
    }

    public function podcastAuthor(): ?string
    {
        return $this->channel->podcastAuthor();
    }

    public function podcastEmail(): ?string
    {
        return $this->channel->podcastEmail();
    }

    public function podcastCopyright(): ?string
    {
        return $this->channel->podcastCopyright();
    }

    public function podcastLanguage(): ?string
    {
        return $this->channel->podcastLanguage();
    }

    public function podcastCategory(): ?Category
    {
        return $this->channel->podcastCategory();
    }

    public function podcastExplicit(): ?string
    {
        return $this->channel->podcastExplicit();
    }

    public function podcastUrl(): string
    {
        return config('app.playlists_url') . '/' . $this->relativeFeedPath();
    }

    public function relativeFeedPath(): string
    {
        return $this->channel->channelId() . '/' . $this->youtube_playlist_id . '.xml';
    }

    public function channelId(): string
    {
        return $this->channel->channelId();
    }

    /**
     * Return the remote path of the podcast feed for this channel.
     *
     * @return string remote path
     */
    public function remoteFilePath(): string
    {
        return config('app.playlists_path') . $this->relativeFeedPath();
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', 1);
    }

    /**
     * will return active playlist(s) for user.
     *
     * @param \App\User $user
     */
    public static function userPlaylists(User $user)
    {
        /** get user channels */
        $channels = Channel::userChannels($user);
        if (!$channels->count()) {
            return new Collection();
        }

        $playlists = new Collection();
        /**
         * get playlist associated with each channel
         * I'm sending $playlist with the use because the double map is
         * getting me a collection (per channel) of collection (per playlist).
         * @todo I should update the database to add user_id into playlists table.
         */
        $channels->map(function (Channel $channel) use ($playlists) {
            return Playlist::active()
                ->where('channel_id', '=', $channel->channel_id)
                ->get()
                ->map(function (Playlist $playlist) use ($playlists) {
                    $playlist->vignetteUrl = Vignette::defaultUrl();
                    if ($playlist->cover) {
                        $playlist->vignetteUrl = Vignette::fromThumb($playlist->cover)->url();
                    }
                    $playlists->push($playlist);
                });
        });
        return $playlists;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function youtubeId(): string
    {
        return $this->youtube_playlist_id;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * I'm using this kind of information everywhere.
     */
    public function nameWithId(): string
    {
        return "{$this->title()} ({$this->id()})";
    }

    public function owner(): Authenticatable
    {
        return $this->channel->user;
    }
}
