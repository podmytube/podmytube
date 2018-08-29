<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thumbs extends Model
{

    protected $fillable = [
        'channel_id',
        'file_name',
        'file_disk',
        'file_size',
    ];
    /** 
	 * This function defines the relation between one thumb and its channel (the channel it is belonging to)  .
	 * @return Object Channel 
	 */
	public function channel()    
    {
    
        return $this->belongsTo(Channel::class, 'channel_id');
    
    }  

    /**
     * This function is checking if one thumbnail is existing for a specific channel.
     * 
     * @return boolean true if thumb present false else.
     */
    public function exists()
    {
        
        return \Storage::disk('thumbs')->exists($this->channel_id.'/'.$this->file_name);

    }
    
    /**
     * return the url of the thumbs for the current channel.
     */

    public function get_url()    
    {
     
        if ( ! $this->exists() )         
        {

            Throw new \Exception ('Thumbs for this channel does not exist');

        }
        
        return \Storage::disk('thumbs')->url($this->channel_id.'/'.$this->file_name);

    }
}
