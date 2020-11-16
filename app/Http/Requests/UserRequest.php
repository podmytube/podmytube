<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // prettier-ignore
            'name' => 'required',
            'email' => 'required|email',
            'language' => 'required|in:fr,en',
            'newsletter' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'language.required' => 'Language is required.',

            'email.email' => 'Email address is not valid.',
            'language.in' => "Languages allowed : 'fr' and 'en' only.",
            'newsletter.boolean' => 'Newsletter value not valid.',
        ];
    }
}
