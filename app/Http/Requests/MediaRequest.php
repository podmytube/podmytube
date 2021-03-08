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

/**
 * the channel form request class
 */
class MediaRequest extends FormRequest
{
    public const TITLE_MAX_LENGTH = 255;

    public function rules()
    {
        return [
            'title' => 'string|max:' . self::TITLE_MAX_LENGTH,
            'description' => 'nullable',
            'media_file' => 'required|file|mimetypes:audio/mpeg',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title of the episode is required',
            'title.max' => 'Title must be shorter or equal ' . self::TITLE_MAX_LENGTH,
            'media_file.required' => 'Audio file of this episode is required.',
            'media_file.file' => 'Audio file should been successfully uploaded',
            'media_file.mimetypes' => 'Only mp3 files are accepted yet',
        ];
    }
}
