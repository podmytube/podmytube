<?php

/**
 * the channel model to access database same table name
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use App\Interfaces\Podcastable;
use App\Podcast\PodcastItem;
use App\Traits\BelongsToCategory;
use App\Traits\BelongsToUser;
use App\Traits\HasLimits;
use App\Traits\HasManyMedias;
use App\Traits\HasManyPlaylists;
use App\Traits\HasOneLanguage;
use App\Traits\HasOneSubscription;
use App\Traits\HasOneThumb;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * the channel model and its functions
 */
class Channel extends Model implements Podcastable
{
    use BelongsToCategory,
        BelongsToUser,
        HasLimits,
        HasManyMedias,
        HasManyPlaylists,
        HasOneSubscription,
        HasOneThumb,
        HasOneLanguage;

    public const CREATED_AT = 'channel_createdAt';
    public const UPDATED_AT = 'channel_updatedAt';

    /** I didn't know about the convention and I bite my hand everytime */
    protected $primaryKey = 'channel_id';
    /** the channel_id is not one auto_increment integer */
    public $incrementing = false;
    /** and it's a string */
    protected $keyType = 'string';

    /**
     * those fields are converted into Carbon mutator
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
     * the field that are guarded
     */
    protected $guarded = [];

    public function channelId():string
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

    public function relativeFeedPath():string
    {
        return $this->channelId() . '/' . config('app.feed_filename');
    }

    /**
     * Return the remote path of the podcast feed for this channel.
     *
     * @return string remote path
     */
    public function remoteFilePath():string
    {
        return config('app.feed_path') . $this->relativeFeedPath();
    }

    /**
     * Return the podcast url for this channel.
     */
    public function podcastUrl():string
    {
        return config('app.podcasts_url') . '/' . $this->relativeFeedPath();
    }

    /**
     * return all early birds channels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function earlyBirdsChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('plan_id', '=', Plan::EARLY_PLAN_ID);
            })
            ->with(['User', 'Category', 'Thumb', 'Subscription'])
            ->get();
    }

    /**
     * return all free channels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function freeChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('plan_id', '=', Plan::FREE_PLAN_ID);
            })
            ->with(['User', 'Category', 'Thumb', 'Subscription'])
            ->get();
    }

    /**
     * return all paying customers channels.
     * Paying customers only.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function payingChannels(): Collection
    {
        return static::active()
            ->whereHas('subscription', function (Builder $query) {
                $query->where('plan_id', '>', Plan::EARLY_PLAN_ID);
            })
            ->with(['User', 'Category', 'Thumb', 'Subscription'])
            ->get();
    }

    public static function allActiveChannels()
    {
        return self::active()
            ->with(['User', 'Category', 'Thumb', 'Subscription'])
            ->get();
    }

    public function hasFilter()
    {
        return $this->accept_video_by_tag !== null || $this->reject_video_by_keyword !== null ||
        $this->reject_video_too_old !== null;
    }

    public function hasAcceptOnlyTags()
    {
        return $this->accept_video_by_tag !== null;
    }

    /**
     * check if tag is in the allowed tags
     */
    public function isTagInAcceptedOnlyTags(string $tag)
    {
        /** if channel has no accept only tag */
        if (!$this->hasAcceptOnlyTags()) {
            return true;
        }

        return in_array(
            $tag,
            array_map('trim', explode(',', $this->accept_video_by_tag))
        );
    }

    public function isTagAccepted(string $tag)
    {
        /** no filter set all medias accepted */
        if (!$this->hasFilter()) {
            return true;
        }

        return $this->isTagInAcceptedOnlyTags($tag);
    }

    public function areTagsAccepted(array $tags)
    {
        /** no filter set all medias accepted */
        if (!$this->hasFilter()) {
            return true;
        }

        foreach ($tags as $tag) {
            if ($this->isTagAccepted($tag)) {
                return true;
            }
        }

        return false;
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

    /**
     * get one channel by its id.
     *
     * @param string $channelId the channel_id you are looking for
     *
     * @return \App\Channel
     */
    public static function byChannelId(string $channelId)
    {
        return self::where('channel_id', '=', $channelId)->first();
    }

    /**
     * get user channels
     *
     * @param User $user
     *
     * @return \App\Channel
     */
    public static function byUserId(Authenticatable $user) : ?Collection
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
    public function id():string
    {
        return $this->channel_id;
    }

    public function isFree()
    {
        return $this->subscription->plan_id == Plan::FREE_PLAN_ID;
    }

    public function nextMediaId()
    {
        return substr(Str::slug($this->channel_name), 0, 20) . '-' . ($this->medias->count() + 1);
    }

    /**
     * I'm using this kind of information everywhere.
     */
    public function nameWithId()
    {
        return "{$this->title()} ({$this->id()})";
    }

    /**
     * Will return the medias to be published.
     * Medias should have been grabbed
     */
    public function mediasToPublish():Collection
    {
        $query = $this->medias()
            ->whereNotNull('grabbed_at')
            ->orderBy('published_at', 'desc');
        if ($this->isFree()) {
            $query->take(3);
        }
        return $query->get();
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
        if (!$this->thumb) {
            return Thumb::defaultUrl();
        }
        return $this->thumb->podcastUrl();
    }

    /**
     * return informations needed to generate podcast header.
     */
    public function podcastHeader():array
    {
        return  [
            'title' => $this->title(),
            'link' => $this->podcastLink(),
            'description' => $this->podcastDescription(),
            'authors' => $this->podcastAuthors(),
            'email' => $this->podcastEmail(),
            'copyright' => $this->podcastCopyright(),
            'imageUrl' => $this->podcastCoverUrl(),
            'language' => $this->podcastLanguage(),
            'category' => $this->podcastCategory(),
            'explicit' => $this->podcastExplicit(),
        ];
    }

    public function toPodcast():array
    {
        return array_merge(
            $this->podcastHeader(),
            ['podcastItems' => $this->podcastItems()]
        );
    }

    public function title():string
    {
        return $this->podcast_title ?? $this->channel_name;
    }

    public function podcastTitle():string
    {
        return $this->title();
    }

    public function podcastLink():?string
    {
        return $this->link;
    }

    public function podcastDescription():?string
    {
        return $this->description;
    }

    public function podcastAuthors():?string
    {
        return $this->authors;
    }

    public function podcastEmail():?string
    {
        return $this->email;
    }

    public function podcastCategory():?Category
    {
        return $this->category;
    }

    public function podcastCopyright():?string
    {
        return $this->podcast_copyright;
    }

    public function podcastLanguage():?string
    {
        return optional($this->language)->code;
    }

    public function podcastExplicit():?string
    {
        return $this->explicit === true ? 'true' : 'false';
    }
}
