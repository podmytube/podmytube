<?php
/**
 * the playlist model to access database same table name
 *
 * Mainly redefine the primary key and the relationship between one channel and its playlist
 *
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * the Playlist class and its functions
 */
class Playlist extends Model
{
  /**
   * the way to specify users.user_id is the key (and not users.id)
   */
  protected $primaryKey = 'playlist_id';
}
