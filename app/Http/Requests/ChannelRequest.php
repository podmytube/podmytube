<?php
/**
 * the form request for channels forms
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Requests;

use App\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * the channel form request class
 */
class ChannelRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'podcast_title' => 'nullable|string|max:64',
            'authors' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            // all the categories are available
            'category_id' => [
                'nullable',
                Rule::in(Category::all()->pluck('id')),
            ],
            'link' => 'nullable|URL',
            'lang' => ['required', Rule::in(['FR', 'EN', 'PT'])],
            'accept_video_by_tag' => 'nullable|string|max:255',
            'reject_video_by_keyword' => 'nullable|string|max:255',
            'reject_video_too_old' => 'nullable|date_format:d/m/Y|before:today',
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
