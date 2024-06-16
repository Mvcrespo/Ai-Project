<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\Payment;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user()->id,
            'nif' => 'nullable|string|min:9|max:9',
            'payment_type' => ['nullable', Rule::in(['VISA', 'PAYPAL', 'MBWAY'])],
            'payment_ref' => 'nullable|string|max:255',
            'photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];


        $paymentRef = $this->input('payment_ref');
        if ($this->input('payment_type') === 'VISA') {
            $rules['payment_ref'] = ['required', 'digits:16', function ($attribute, $value, $fail) use ($paymentRef) {
                if (!Payment::payWithVisa($paymentRef, '123')) {
                    $fail('The payment reference is not valid for VISA.');
                }
            }];
        } elseif ($this->input('payment_type') === 'PAYPAL') {
            $rules['payment_ref'] = ['required', 'email', 'max:255', function ($attribute, $value, $fail) use ($paymentRef) {
                if (!Payment::payWithPaypal($paymentRef)) {
                    $fail('The payment reference is not valid for PAYPAL.');
                }
            }];
        } elseif ($this->input('payment_type') === 'MBWAY') {
            $rules['payment_ref'] = ['required', 'regex:/^9\d{8}$/', function ($attribute, $value, $fail) use ($paymentRef) {
                if (!Payment::payWithMBway($paymentRef)) {
                    $fail('The payment reference is not valid for MBWAY.');
                }
            }];
        } elseif ($this->input('payment_type') === null) {
            $rules['payment_ref'] = 'prohibited';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'payment_ref.required' => 'The payment reference is required when a payment type is selected.',
            'payment_ref.digits' => 'The payment reference must be 16 digits long for VISA.',
            'payment_ref.email' => 'The payment reference must be a valid email address for PAYPAL.',
            'payment_ref.regex' => 'The payment reference must be a valid Portuguese mobile phone number (9 digits, starting with 9) for MBWAY.',
            'payment_ref.prohibited' => 'The payment reference must be null when no payment type is selected.',
        ];
    }
}
