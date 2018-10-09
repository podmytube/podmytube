<?php

/**
 * the channel model to access database same table name
 *
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * the channel class and its functions
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
        'reject_video_too_old'
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
        'category',
        'subcategory',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * define the relationship between one channel and one subscription
     *
     * @return model the current subscription
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id');
    }

    /**
     * define the relationship between one channel and its playlists
     *
     */
    public function playlists()
    {
        return $this->HasMany(Playlist::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its medias
     *
     */
    public function medias()
    {
        return $this->HasMany(Medias::class, 'channel_id');
    }

    /**
     * one channel has many medias stats
     *
     */
    public function medias_stats()
    {
        return $this->HasMany(MediasStats::class, 'channel_id');
    }

    /**
     * define the relationship between one channel and its playlists
     *
     */
    public function thumbs()
    {
        return $this->HasOne("App\Thumbs", 'channel_id');
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
     *
     * @return string the channel id
     */
    public static function extractChannelIdFromUrl($url)
    {
        /**
         * url should be one
         */
        if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            return false;
        }

        /**
         * checking the url given.
         * It should contain one youtube url the channel path and the channel_id
         */
        if (!preg_match("/^https?:\/\/(youtube.com|www.youtube.com)\/channel\/(?'channel'[\w\-]*)$/", $url, $matches)) {
            return false;
        } else {
            return $matches['channel'];
        }
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



    /**
     * Providing all the apple itunes podcast categories
     *
     */
    public static function categories()
    {
        return [
            'Arts',
            'Business',
            'Comedy',
            'Education',
            'Games &amp; Hobbies',
            'Government &amp; Organizations',
            'Health',
            'Kids &amp; Family',
            'Music',
            'News &amp; Politics',
            'Religion &amp; Spirituality',
            'Science &amp; Medicine',
            'Society &amp; Culture',
            'Sports &amp; Recreation',
            'Technology',
            'TV &amp; Film',
        ];
    }
}
