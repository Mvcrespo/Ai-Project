<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ticket_price' => 'required|numeric',
            'registered_customer_ticket_discount' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'ticket_price.required' => 'The ticket price is required.',
            'ticket_price.numeric' => 'The ticket price must be a number.',
            'registered_customer_ticket_discount.required' => 'The registered customer ticket discount is required.',
            'registered_customer_ticket_discount.numeric' => 'The registered customer ticket discount must be a number.',
        ];
    }
}
