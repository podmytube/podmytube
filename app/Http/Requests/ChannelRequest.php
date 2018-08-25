<?php
/**
 * the form request for channels forms
 * 
 * @package PodMyTube
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Channel;


/**
 * the channel form request class 
 */
class ChannelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
       
        $user = app( 'auth' )->user();
                
        return $this->channel->user_id === $user->user_id;
                
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @todo check why if I add lang in the rules everything is collapsing
     * @todo check the messages by trying to fool the form
     */
    public function rules()
    {
        return [
            //'channel_id' 	            => 'required|max:255',
            'podcast_title'             => 'nullable|string|max:64',
            'authors' 		            => 'sometimes|string|max:255',
            'email' 		            => 'sometimes|email',
            'category'		            => 'nullable|string',
            'subcategory'		        => 'nullable|string',
            'link' 			            => 'nullable|URL',
            //'lang' 			            => 'required',
            'explicit' 		            => 'nullable',
            'accept_video_by_tag' 		=> 'nullable|string|max:255',
            'reject_video_by_keyword' 	=> 'nullable|string|max:255',
            'reject_video_too_old' 		=> 'nullable|date_format:d/m/Y|before:today',
        ];
    }
        
    /**
     * The message to send when rule is failing
     *
     * @return messages to display to the users
     */
/*    public function messages()
    {
        return [
            'podcast_title.required' => 'if you want'
        ];
    }
*/
}
