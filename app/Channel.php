<?php

declare(strict_types=1);

/**
 * the channel model to access database same table name.
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use App\Interfaces\Coverable;
use App\Interfaces\Podcastable;
use App\Podcast\PodcastItem;
use App\Traits\BelongsToCategory;
use App\Traits\BelongsToUser;
use App\Traits\HasCover;
use App\Traits\HasLimits;
use App\Traits\HasManyMedias;
use App\Traits\HasManyPlaylists;
use App\Traits\HasOneLanguage;
use App\Traits\HasOneSubscription;
use App\Traits\IsRelatedToOneChannel;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * the channel model and its functions.
 */
class Channel extends Model implements Podcastable, Coverable
{
    use BelongsToCategory;
    use BelongsToUser;
    use HasLimits;
    use HasManyMedias;
    use HasManyPlaylists;
    use HasOneSubscription;
    use HasOneLanguage;
    use HasCover;
    use IsRelatedToOneChannel;

    public const CREATED_AT = 'channel_createdAt';
    public const UPDATED_AT = 'channel_updatedAt';
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
        'channel_createdAt',
        'channel_updatedAt',
        'podcast_updatedAt',
        'reject_video_too_old',
    ];

    protected $casts = [
        'explicit' => 'boolean',
    ];

    /**
     * the field that are guarded.
     */
    protected $guarded = [];

    public function channelId(): string
    {
        return $this->channel_id;
    }

    public function userId()
    {
        return $this->user_id;
    }

    public function createdAt()
    {
        return $this->channel_createdAt;
    }

    public function podcastUpdatedAt()
    {
        return $this->podcast_updatedAt;
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

    /**
     * return all early birds channels.
     */
    public static function earlyBirdsChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query): void {
                $query->where('plan_id', '=', Plan::EARLY_PLAN_ID);
            })
            ->with(['User', 'Category', 'cover', 'Subscription'])
            ->get()
        ;
    }

    /**
     * return all free channels.
     */
    public static function freeChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query): void {
                $query->where('plan_id', '=', Plan::FREE_PLAN_ID);
            })
            ->with(['User', 'Category', 'cover', 'Subscription'])
            ->get()
        ;
    }

    /**
     * return all paying customers channels.
     * Paying customers only.
     */
    public static function payingChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query): void {
                $query->where('plan_id', '>', Plan::EARLY_PLAN_ID);
            })
            ->with(['User', 'Category', 'cover', 'Subscription'])
            ->get()
        ;
    }

    public static function allActiveChannels(): Collection
    {
        return self::active()
            ->with(['User', 'Category', 'cover', 'Subscription'])
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
            //"accept only videos with tag " . $this->accept_video_by_tag;
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
        $channelsCollection = self::where('user_id', '=', $user->user_id)->get();
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
        return $this?->subscription?->plan_id === Plan::FREE_PLAN_ID;
    }

    public function isPaying(): bool
    {
        return !in_array($this->subscription->plan_id, [Plan::FREE_PLAN_ID, Plan::EARLY_PLAN_ID]);
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
        return self::where('user_id', '=', $user->user_id)->get();
    }

    public function youtubeId(): string
    {
        return $this->channelId();
    }

    public function subscribeToPlan(Plan $plan): Subscription
    {
        return Subscription::updateOrCreate(
            ['channel_id' => $this->channel_id],
            ['plan_id' => $plan->id]
        );
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

    public static function nbReallyActiveChannels()
    {
        return Channel::active()
            ->whereHas('medias', function ($query): void {
                $query->whereNotNull('grabbed_at')
                    ->whereBetween('grabbed_at', [
                        now()->startOfMonth(),
                        now(),
                    ])
            ;
            })
            ->count()
        ;
    }
}
