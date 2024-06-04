<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'genre_code' => 'required|string|max:20',
            'year' => 'required|integer',
            'poster_filename' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'synopsis' => 'required|string',
            'trailer_url' => 'nullable|url|max:255',
        ];
    }
}
