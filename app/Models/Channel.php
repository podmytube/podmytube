<?php

declare(strict_types=1);

/**
 * the channel model to access database same table name.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Models;

use App\Interfaces\Coverable;
use App\Interfaces\Podcastable;
use App\Podcast\PodcastItem;
use App\Traits\BelongsToCategory;
use App\Traits\BelongsToUser;
use App\Traits\HasCover;
use App\Traits\HasDownloads;
use App\Traits\HasLimits;
use App\Traits\HasManyMedias;
use App\Traits\HasManyPlaylists;
use App\Traits\HasOneLanguage;
use App\Traits\HasOneSubscription;
use App\Traits\HasVignette;
use App\Traits\IsRelatedToOneChannel;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @property string       $accept_video_by_tag
 * @property string       $authors
 * @property bool         $active
 * @property Category     $category
 * @property Carbon       $created_at
 * @property Carbon       $updated_at
 * @property string       $channel_name
 * @property string       $description
 * @property string       $email
 * @property bool         $explicit
 * @property Language     $language
 * @property string       $link
 * @property Carbon       $podcast_updated_at
 * @property Carbon       $reject_video_too_old
 * @property string       $reject_video_by_keyword
 * @property string       $podcast_copyright
 * @property string       $podcast_title
 * @property Subscription $subscription
 * @property User         $user
 */
class Channel extends Model implements Podcastable, Coverable
{
    use BelongsToCategory;
    use BelongsToUser;
    use HasLimits;
    use HasFactory;
    use HasDownloads;
    use HasManyMedias;
    use HasManyPlaylists;
    use HasOneSubscription;
    use HasOneLanguage;
    use HasCover;
    use HasVignette;
    use IsRelatedToOneChannel;

    public const REMOTE_DISK = 'remote';
    public const DEFAULT_CATEGORY_SLUG = 'society-culture';

    /** the channel_id is not one auto_increment integer */
    public $incrementing = false;

    /** I didn't know about the convention and I bite my hand everytime */
    protected $primaryKey = 'channel_id';

    /** and it's a string */
    protected $keyType = 'string';

    /**
     * those fields are converted into Carbon mutator.
     */
    protected $dates = [
        'podcast_updated_at',
        'reject_video_too_old',
    ];

    protected $casts = [
        'active' => 'boolean',
        'explicit' => 'boolean',
    ];

    /**
     * the field that are guarded.
     */
    protected $guarded = ['id'];

    public function channelId(): string
    {
        return $this->channel_id;
    }

    public function relativeFeedPath(): string
    {
        return $this->channelId() . '/' . config('app.feed_filename');
    }

    /**
     * Return the remote path of the podcast feed for this channel.
     *
     * @return string remote path
     */
    public function remoteFilePath(): string
    {
        return config('app.feed_path') . $this->relativeFeedPath();
    }

    /**
     * Return the podcast url for this channel.
     */
    public function podcastUrl(): string
    {
        return config('app.podcasts_url') . '/' . $this->relativeFeedPath();
    }

    public static function allActiveChannels(): Collection
    {
        return self::with(['user', 'category', 'cover', 'subscription'])
            ->active()
            ->get()
        ;
    }

    public function hasFilter(): bool
    {
        return $this->accept_video_by_tag !== null
            || $this->reject_video_by_keyword !== null
            || $this->reject_video_too_old !== null;
    }

    public function hasAcceptOnlyTags(): bool
    {
        return $this->accept_video_by_tag !== null;
    }

    /**
     * check if tag is in the allowed tags.
     */
    public function isTagAccepted(?string $tag = null): bool
    {
        // if channel has no accept only tag
        if (!$this->hasAcceptOnlyTags()) {
            Log::debug('Channel has no accept only filter => accept');

            return true;
        }

        // tag is empty or null => rejected
        if ($tag === null || strlen($tag) <= 0) {
            if ($this->hasAcceptOnlyTags()) {
                Log::debug("Tag ---{$tag}--- is empty BUT owner accept only ---{$this->accept_video_by_tag}~~~ => rejected.");

                return false;
            }
            Log::debug("Tag ---{$tag}--- is empty BUT owner accept all tags => accepted.");

            return true;
        }

        return in_array(
            $tag,
            array_map('trim', explode(',', $this->accept_video_by_tag))
        );
    }

    public function areTagsAccepted(array $tags = []): bool
    {
        // no filter set all medias accepted
        if (!$this->hasFilter()) {
            Log::debug('Channel has no filters => accepted');

            return true;
        }

        if (count($tags)) {
            $result = array_filter($tags, [$this, 'isTagAccepted']);
            if (count($result)) {
                return true;
            }
        }

        // arriving here means there is no tag
        if ($this->hasAcceptOnlyTags()) {
            // No tag specified BUT owner accept only some TAG => rejected.
            return false;
        }
        // No tag specified but no filtering => accepted
        return true;
    }

    public function isDateAccepted(Carbon $date): bool
    {
        if ($this->reject_video_too_old === null) {
            return true;
        }

        return $date->isAfter($this->reject_video_too_old);
    }

    public function getFilters()
    {
        $results = [];
        if (!$this->hasFilter()) {
            return $results;
        }
        if ($this->accept_video_by_tag !== null) {
            $results[] = Lang::get('messages.accept_video_by_tag', [
                'tag' => $this->accept_video_by_tag,
            ]);
            // "accept only videos with tag " . $this->accept_video_by_tag;
        }
        if ($this->reject_video_by_keyword !== null) {
            $results[] = Lang::get('messages.reject_video_by_keyword', [
                'keyword' => $this->reject_video_by_keyword,
            ]);
        }
        if ($this->reject_video_too_old !== null) {
            $results[] = Lang::get('messages.reject_video_too_old', [
                'date' => $this->reject_video_too_old->format(
                    Lang::get('localized.dateFormat')
                ),
            ]);
        }

        return $results;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', 1);
    }

    public static function byUserId(Authenticatable $user): ?Collection
    {
        $channelsCollection = self::where('user_id', '=', $user->id)->get();
        if ($channelsCollection->count()) {
            return $channelsCollection;
        }

        return null;
    }

    /**
     * @return string channel_id of channel
     */
    public function id(): string
    {
        return $this->channelId();
    }

    public function isFree(): bool
    {
        return !$this->isPaying();
    }

    public function isPaying(): bool
    {
        return $this->plan->slug !== 'forever_free';
    }

    public function slugChannelName(): string
    {
        return substr(Str::slug($this->channel_name), 0, 20);
    }

    public function nextMediaId()
    {
        return uniqid($this->slugChannelName() . '-');
    }

    /**
     * I'm using this kind of information everywhere.
     */
    public function nameWithId(): string
    {
        return "{$this->title()} ({$this->id()})";
    }

    /**
     * Will return the medias to be published.
     * Medias should have been grabbed.
     */
    public function mediasToPublish(): Collection
    {
        $query = $this->medias()
            ->whereNotNull('grabbed_at')
            ->orderBy('published_at', 'desc')
        ;
        if ($this->isFree()) {
            $query->take(3);
        }

        return $query->get();
    }

    public function podcastItems(): SupportCollection
    {
        return $this->mediasToPublish()
            ->map(function (Media $media) {
                return PodcastItem::with($media->toPodcastItem());
            })
        ;
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
            'title' => $this->title(),
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

    public function title(): string
    {
        return $this->podcast_title ?? $this->channel_name;
    }

    public function podcastTitle(): string
    {
        return $this->title();
    }

    public function podcastLink(): ?string
    {
        return $this->link;
    }

    public function podcastDescription(): ?string
    {
        return $this->description;
    }

    public function podcastAuthor(): ?string
    {
        return $this->authors;
    }

    public function podcastEmail(): ?string
    {
        return $this->email;
    }

    public function podcastCategory(): ?Category
    {
        return $this->category;
    }

    public function podcastCopyright(): ?string
    {
        return $this->podcast_copyright;
    }

    public function podcastLanguage(): ?string
    {
        return optional($this->language)->code;
    }

    public function podcastExplicit(): ?string
    {
        return $this->explicit === true ? 'true' : 'false';
    }

    public function shouldChannelBeUpgraded(?int $month = null, ?int $year = null)
    {
        if ($this->isFree()) {
            return true;
        }

        return $this->hasReachedItslimit($month, $year);
    }

    public static function userChannels(User $user)
    {
        return self::where('user_id', '=', $user->id)->get();
    }

    /**
     * used as a property $this->youtube_id.
     */
    public function youtubeId(): Attribute
    {
        return Attribute::get(fn () => $this->channel_id);
    }

    public function associatedMedias(): Collection
    {
        return $this->medias;
    }

    public static function byYoutubeId(string $youtubeId): ?self
    {
        return self::byChannelId($youtubeId);
    }

    /**
     * Return the youtube url for this channel.
     */
    public function youtubeUrl(): string
    {
        return 'https://www.youtube.com/channel/' . $this->channelId();
    }

    /**
     * tell if channel has medias added in the last 5 minutes.
     */
    public function hasRecentlyAddedMedias(int $nbMinutesAgo = 5): bool
    {
        return self::whereHas('medias', function (Builder $query) use ($nbMinutesAgo): void {
            $query->where('created_at', '>', now()->subMinutes($nbMinutesAgo));
        })->exists();
    }

    public function wasUpdatedOn(Carbon $updatedOnDate): bool
    {
        return $this->update(['podcast_updated_at' => $updatedOnDate]);
    }

    public function relativeFolderPath(): string
    {
        return $this->channelId();
    }

    public function feedFolderPath(): string
    {
        return config('app.feed_path') . $this->relativeFolderPath();
    }

    public function mp3FolderPath(): string
    {
        return config('app.mp3_path') . $this->relativeFolderPath();
    }

    public function playlistFolderPath(): string
    {
        return config('app.playlists_path') . $this->relativeFolderPath();
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public static function userChannelsOptimized(User $user): Collection
    {
        return Channel::query()
            ->select('user_id', 'channel_id', 'channel_name', 'podcast_title', 'active')
            ->where('user_id', '=', $user->id)
            ->with([
                'playlists:channel_id,active',
                'subscription:channel_id,plan_id',
                'subscription.plan:id,name',
            ])
            ->get()
        ;
    }
}
