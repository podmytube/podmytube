<?php

/**
 * the channel model to access database same table name
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use App\Exceptions\ChannelCreationInvalidChannelUrlException;
use App\Exceptions\ChannelCreationInvalidUrlException;
use App\Exceptions\ChannelCreationOnlyYoutubeIsAccepted;
use App\Podcast\PodcastBuilder;
use App\Traits\HasLimits;
use App\Traits\HasManyMedias;
use App\Traits\HasOneCategory;
use App\Traits\HasOneThumb;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

/**
 * the channel model and its functions
 */
class Channel extends Model
{
    use HasLimits, HasManyMedias, HasOneThumb, HasOneCategory;

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
     * define the relationship between one user and one channel.
     *
     * @return object the user that own this channel
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

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
     * mutator in order to convert input received data from d/m/Y to Y-m-d before to send it in db
     *
     * @param date d/m/Y format waited
     */
    public function setRejectVideoTooOldAttribute($date)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $date);
        if ($date !== false) {
            $this->attributes['reject_video_too_old'] = $date->format('Y-m-d');
        } else {
            $this->attributes['reject_video_too_old'] = null;
        }
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
        return self::where([
            ['active', 1],
            ['subscriptions.plan_id', '=', Plan::EARLY_PLAN_ID],
        ])
            ->with('User')
            ->with('Category')
            ->with('Thumb')
            ->with('Subscription')
            ->join(
                'subscriptions',
                'subscriptions.channel_id',
                '=',
                'channels.channel_id'
            )
            ->get();
    }

    /**
     * return all free channels.
     *
     * @return Illuminate\Support\Collection
     */
    public static function freeChannels(): Collection
    {
        return self::where([
            ['active', 1],
            ['subscriptions.plan_id', '=', Plan::FREE_PLAN_ID],
        ])
            ->with('User')
            ->with('Category')
            ->with('Thumb')
            ->with('Subscription')
            ->join(
                'subscriptions',
                'subscriptions.channel_id',
                '=',
                'channels.channel_id'
            )
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
        return self::where([
            ['active', 1],
            ['subscriptions.plan_id', '>', Plan::EARLY_PLAN_ID],
        ])
            ->with('User')
            ->with('Category')
            ->with('Thumb')
            ->with('Subscription')
            ->join(
                'subscriptions',
                'subscriptions.channel_id',
                '=',
                'channels.channel_id'
            )
            ->get();
    }

    public static function allActiveChannels()
    {
        return self::where('active', 1)
            ->with('User')
            ->with('Category')
            ->with('Thumb')
            ->with('Subscription')
            ->get();
    }

    public function hasFilter()
    {
        return (isset($this->accept_video_by_tag) &&
            $this->accept_video_by_tag !== null) ||
            (isset($this->reject_video_by_keyword) &&
                $this->reject_video_by_keyword !== null) ||
            (isset($this->reject_video_too_old) &&
                $this->reject_video_too_old !== null);
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

    public function scopeActive($query)
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
}
