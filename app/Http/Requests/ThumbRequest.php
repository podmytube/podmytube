<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThumbRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->channel->user_id === $this->user()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // prettier-ignore
            'new_thumb_file' => 'required|dimensions:min_width=1400,min_height=1400,max_width=3000,max_height=3000,ratio=1',
        ];
    }

    public function messages()
    {
        return [
            'required' => __('messages.thumbs_edit_error_image_required'),
            'dimensions' => __('messages.thumbs_edit_error_image_dimensions'),
        ];
    }
}
