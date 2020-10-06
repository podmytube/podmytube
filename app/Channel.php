<?php

/**
 * the channel model to access database same table name
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use App\Podcast\PodcastBuilder;
use App\Traits\BelongsToUser;
use App\Traits\HasLimits;
use App\Traits\HasManyMedias;
use App\Traits\HasOneCategory;
use App\Traits\HasOneThumb;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

/**
 * the channel model and its functions
 */
class Channel extends Model
{
    use HasLimits, HasManyMedias, HasOneThumb, HasOneCategory, BelongsToUser;

    public const CREATED_AT = 'channel_createdAt';
    public const UPDATED_AT = 'channel_updatedAt';

    /**
     * the way to specify users.user_id is the key (and not users.id)
     */
    protected $primaryKey = 'channel_id';

    /**
     * the channel_id is not one auto_increment integer
     */
    public $incrementing = false;

    /**
     * those fields are converted into Carbon mutator
     */
    protected $dates = [
        'channel_createdAt',
        'channel_updatedAt',
        'podcast_updatedAt',
        'reject_video_too_old',
    ];

    /**
     * the field that are guarded
     */
    protected $guarded = [];

    /**
     * We are getting active subscriptions for the channel.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }

    /**
     * Provides the channel global podcast url
     *
     * @return string the podcast url
     */
    public function getFeedUrlAttribute()
    {
        return env('PODCAST_URL') . '/' . $this->channel_id . '/podcast.xml';
    }

    /**
     * Getter : channel_id
     */
    public function channelId()
    {
        return $this->channel_id;
    }

    public function userId()
    {
        return $this->user_id;
    }

    public function title()
    {
        return $this->podcast_title ?? $this->channel_name;
    }

    public function explicit(): bool
    {
        return $this->explicit === 1 ? true : false;
    }

    public function createdAt()
    {
        return $this->channel_createdAt;
    }

    public function podcastUpdatedAt()
    {
        return $this->podcast_updatedAt;
    }

    public function podcastUrl()
    {
        return getenv('PODCASTS_URL') .
        DIRECTORY_SEPARATOR .
        $this->channelId() .
        DIRECTORY_SEPARATOR .
        PodcastBuilder::FEED_FILENAME;
    }

    /**
     * return all early birds channels.
     *
     * @return Illuminate\Support\Collection
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
     * @return Illuminate\Support\Collection
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
     * @return Illuminate\Support\Collection
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

    public static function byPlanType(string $planType): Collection
    {
        return Channel::select('channel_id', 'channel_name')
            ->whereHas('subscription', function (
                \Illuminate\Database\Eloquent\Builder $query
            ) use ($planType) {
                switch ($planType) {
                    case 'free':
                        $query->where('plan_id', '=', Plan::FREE_PLAN_ID);
                        break;
                    case 'paying':
                        $query->whereNotIn('plan_id', [
                            Plan::FREE_PLAN_ID,
                            Plan::EARLY_PLAN_ID,
                        ]);
                        break;
                    default:
                        break;
                }
            })
            ->get();
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
}
