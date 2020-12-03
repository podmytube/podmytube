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
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'newsletter' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => "I'm sorry but I would like to know your firstname.",
            'lastname.required' => "I'm sorry but I would like to know your lastname.",
            'email.required' => "I swear I won't use it to spam you but I need your email.",

            'email.email' => 'It seems the email address you wrote is not valid.',
            'newsletter.boolean' => 'Do not play with my newsletter. Please :).',
        ];
    }
}
