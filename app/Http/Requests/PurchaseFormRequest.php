<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'nif' => 'required|string|max:9',
            'payment_type' => 'required|string|in:PAYPAL,VISA,MBWAY',
            'payment_reference' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'nif.required' => 'The NIF field is required.',
            'payment_type.required' => 'The payment type is required.',
            'payment_reference.required' => 'The payment reference is required.',
        ];
    }
}
