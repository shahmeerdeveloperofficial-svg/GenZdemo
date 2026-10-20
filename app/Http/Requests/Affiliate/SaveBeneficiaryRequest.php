<?php

namespace App\Http\Requests\Affiliate;

use Illuminate\Foundation\Http\FormRequest;

class SaveBeneficiaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $type = $this->input('type');

        $rules = [
            'beneficiary_name' => 'bail|required|string|min:2',
            'type' => 'bail|required',
        ];

        if ($type == BENEFICIARY_CARD) {
            $rules['card_number'] = 'bail|required|regex:/^[\d\s\-]+$/';
            $rules['card_holder_name'] = 'bail|required|string';
            $rules['expire_month'] = 'bail|required';
            $rules['expire_year'] = 'bail|required';
        } elseif ($type == BENEFICIARY_BANK) {
            $rules['bank_name'] = 'bail|required';
            $rules['bank_account_name'] = 'bail|required|string';
            $rules['bank_account_number'] = 'bail|required|numeric';
            $rules['bank_routing_number'] = 'bail|required';
        } elseif ($type == BENEFICIARY_PAYPAL) {
            $rules['paypal_email'] = 'bail|required|email';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'beneficiary_name.required' => __('Beneficiary Name is Required'),
            'beneficiary_name.min' => __('Beneficiary Name should at least 2 characters'),
            'type.required' => __('Beneficiary Type is Required'),
            'card_number.required' => __('Card Number is Required'),
            'card_number.regex' => __('Card Number can only contain digits, spaces, and hyphens'),
            'card_holder_name.required' => __('Card Holder Name is Required'),
            'expire_month.required' => __('Expired Month is Required'),
            'expire_year.required' => __('Expired Year is Required'),
            'bank_name.required' => __('Bank Name is Required'),
            'bank_account_name.required' => __('Bank Account Name is Required'),
            'bank_account_number.required' => __('Bank Account Number is Required'),
            'bank_routing_number.required' => __('Routing Number is Required'),
            'paypal_email.required' => __('Paypal Email is Required'),
            'paypal_email.email' => __('Please enter a valid email address'),
        ];
    }
}