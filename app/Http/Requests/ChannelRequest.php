<?php
/**
 * the form request for channels forms
 *
 * @package PodMyTube
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * the channel form request class
 */
class ChannelRequest extends FormRequest
{
    protected $supportedLanguages = ['FR', 'EN', 'PT'];

    public function rules()
    {
        return [
            'podcast_title' => 'nullable|max:255',
            'authors' => 'nullable|max:255',
            'description' => 'nullable|max:65535',
            'email' => 'nullable|email',
            'category_id' => 'nullable|exists:\App\Category,id',
            'link' => 'nullable|URL',
            'lang' => ['nullable', Rule::in($this->supportedLanguages)],
            'accept_video_by_tag' => 'nullable|max:255',
            'reject_video_by_keyword' => 'nullable|max:255',
            'reject_video_too_old' => 'nullable|date_format:d/m/Y|before:today',
            'explicit' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'podcast_title.required' => 'The title of your great podcast is missing.',
            'podcast_title.max' => 'The title of your great podcast is too long (255 characters max).',
            'authors.max' => 'Please stop kidding me, your name is too long ... Are you still human ?',
            'email.email' => 'Please give use a valid email address so that your listener can send you some feedback.',
            'category_id.exists' => 'The category you selected is not valid.',
            'link.u_r_l' => 'The link to get more information is not valid. It should be like https://my-greatpodcast.com. Don\'t forget the "http" stuff !',
            'lang.in' => 'The language you selected is not valid. Only ' . implode(', ', $this->supportedLanguages) . ' are supported yet.',
            'explicit.boolean' => 'I\'m not quite sure about your explicit content. Please tell us about it.',
        ];
    }
}
