<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
        $rules = [
            'first_name' => ['required', 'max:120'],
            'last_name' => ['required', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->id],
            'mobile' => ['required', 'min:6'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web')
                    ->where('status', STATUS_ACTIVE),
            ],
        ];

        // Check if this is an update request by checking if $this->id is present
        if ($this->id) {
            $rules['password'] = ['nullable'];
        } else {
            $rules['password'] = ['required'];
        }

        return $rules;
    }
}
