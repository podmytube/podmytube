<?php

/**
 * the channel model to access database same table name
 *
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;


/**
 * the channel model and its functions
 */
class Channel extends Model
{

    /**
     * the way to specify users.user_id is the key (and not users.id)
     */
    protected $primaryKey = 'channel_id';

    /**
     * the channel_id is not one auto_increment integer
     */
    public $incrementing = false;

    /**
     * Laravel is updating the created_at default field on the first record.
     * this way our custom field channel_createdAt is correctly used
     */
    const CREATED_AT = 'channel_createdAt';

    /**
     * Laravel is updating the updated_at default field on every update of the record.
     * this way our custom field channel_updatedAt is correctly used
     */
    const UPDATED_AT = 'channel_updatedAt';

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
        'category_id',
        'link',
        'lang',
        'podcast_title',
        'accept_video_by_tag',
        'reject_video_by_keyword',
        'reject_video_too_old',
        'ftp_host',
        'ftp_user',
        'ftp_pass',
        'ftp_podcast',
        'ftp_dir',
        'ftp_pasv',
    ];

    /**
     * define the relationship between one user and one channel
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
     *
     * @return model the current subscription
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its playlists
     */
    /*
    public function playlists()
    {
        return $this->HasMany(Playlist::class, 'channel_id');
    }
    */

    /**
     * define the relationship between one channel and its medias
     *
     */
    public function medias()
    {
        return $this->HasMany(Media::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its playlists
     *
     */
    public function thumb()
    {
        return $this->HasOne(Thumb::class, 'channel_id');
    }

    /**
     * Provides the channel global podcast url
     *
     * @param Object $channel the channel we need the url for
     * @return string the podcast url
     */
    public static function podcastUrl($channel)
    {
        return $_ENV['APP_PODCAST_URL'] . '/' . $channel->channel_id;
    }

    /**
     * Provides the channel global youtube url
     *
     * @param Object $channel the channel we need the url for
     * @return string the podcast url
     */
    public static function youtubeUrl($channel)
    {
        return 'https://www.youtube.com/channel/' . $channel->channel_id;
    }

    /**
     * Provides the channel pic
     *
     * @param Object $channel the channel we need the picture
     *
     * @return string the picture url
     */
    public static function pictureUrl($channel)
    {
        return $_ENV['APP_PODCAST_URL'] . '/' . $channel->channel_id;
    }

    /**
     * extract the id from a youtube channel url after checkingits valid
     * https://www.youtube.com/channel/UCZ0o1IeuSSceEixZbSATWtw => UCZ0o1IeuSSceEixZbSATWtw
     * @param Request $request the request object 
     * @return string the channel id
     */
    public static function extractChannelIdFromUrl(Request $request)
    {
        /**
         * url should be one
         */
        if (!filter_var($request->channel_url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            throw new \InvalidArgumentException("flash_channel_id_is_invalid");
        }

        /**
         * checking the url given.
         * It should contain one youtube url the channel path and the channel_id
         */
        if (!preg_match("/^https?:\/\/(youtube.com|www.youtube.com)\/channel\/(?'channel'[\w\-]*)$/", $request->channel_url, $matches)) {
            throw new \InvalidArgumentException("flash_channel_id_is_invalid");
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
        if ($date = \DateTime::createFromFormat('d/m/Y', $date)) {
            $this->attributes['reject_video_too_old'] = $date->format('Y-m-d');
        } else {
            $this->attributes['reject_video_too_old'] = null;
        }
    }
}
