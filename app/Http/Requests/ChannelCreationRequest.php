<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChannelCreationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'channel_url' => 'required|string|min:27',
        ];
    }

    public function messages()
    {
        return [
            'channel_url.min' => 'Your channel url should look like this one https://www.youtube.com/channel/UCWfW8a2ejHzRM_acJcJSbtQ. 😥',
            'channel_url.required' => 'You should give us the youtube url of your channel. I feel useless without it. 😥',
            'owner.required' => 'You forgot to swear you are the real owner of this channel. Are you ? 🤔',
        ];
    }
}
