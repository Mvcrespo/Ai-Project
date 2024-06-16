<?php

namespace App\Http\Requests;

use App\Models\Theater;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TheaterFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (strtolower($this->getMethod()) == 'post') {
            $this->merge([
                'user' => null,
            ]);
        } else {
            $this->merge([
                'user' => $this->route('theater')->user,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255|unique:theaters,name,'.($this->theater?$this->theater->id:null),
            'photo_file' => 'sometimes|image|max:4096',
        ];
    }
}
