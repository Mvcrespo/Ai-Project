<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user),
            ],
            'type' => [
                'required',
                Rule::in(['A', 'E', 'C']),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }
}
