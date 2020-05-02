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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

/**
 * the channel model and its functions
 */
class Channel extends Model
{
    use HasLimits;

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
     * the field that can be massAssignemented
     */
    protected $fillable = [
        'channel_id',
        'channel_name',
        'user_id',
        'authors',
        'email',
        'description',
        'category_id',
        'link',
        'lang',
        'explicit',
        'podcast_title',
        'podcast_copyright',
        'accept_video_by_tag',
        'reject_video_by_keyword',
        'reject_video_too_old',
        'channel_createdAt',
        'channel_updatedAt',
        'podcast_updatedAt',
        'ftp_host',
        'ftp_user',
        'ftp_pass',
        'ftp_podcast',
        'ftp_dir',
        'ftp_pasv',
    ];

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
     * Channel should have only one category.
     */
    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /**
     * We are getting active subscriptions for the channel.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its medias
     */
    public function medias()
    {
        return $this->HasMany(Media::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its playlists
     */
    public function thumb()
    {
        return $this->HasOne(Thumb::class, 'channel_id');
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
     * extract the id from a youtube channel url after checkingits valid
     * https://www.youtube.com/channel/UCZ0o1IeuSSceEixZbSATWtw => UCZ0o1IeuSSceEixZbSATWtw.
     *
     * @param string $channelUrl the url of the channel to register
     *
     * @return string the channel id
     */
    public static function extractChannelIdFromUrl(string $channelUrl)
    {
        /**
         * url should be one
         */
        if (
            !filter_var(
                $channelUrl,
                FILTER_VALIDATE_URL,
                FILTER_FLAG_PATH_REQUIRED
            )
        ) {
            throw new ChannelCreationInvalidUrlException(
                'flash_channel_id_is_invalid'
            );
        }

        if (
            !in_array(parse_url($channelUrl, PHP_URL_HOST), [
                'youtube.com',
                'www.youtube.com',
            ])
        ) {
            throw new ChannelCreationOnlyYoutubeIsAccepted(
                'Only channels from youtube are accepted !'
            );
        }

        /**
         * checking the url given.
         * It should contain one youtube url the channel path and the channel_id
         */
        if (
            !preg_match(
                "#^/channel/(?'channel'[A-Za-z0-9_-]*)/?$#",
                parse_url($channelUrl, PHP_URL_PATH),
                $matches
            )
        ) {
            throw new ChannelCreationInvalidChannelUrlException(
                'flash_channel_id_is_invalid'
            );
        }

        return $matches['channel'];
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

    public static function filterByPlanType(string $planType): Collection
    {
        $foo=[
            'free' => Plan::where('id','=',Plan::FREE_PLAN_ID);
        ];
        return Channel::active()
            ->get()
            ->filter(function ($channel) use ($plan) {
                return $channel->subscription->plan_id === $plan->id;
            });
    }
}
