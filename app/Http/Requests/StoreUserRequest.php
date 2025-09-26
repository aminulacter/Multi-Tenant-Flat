<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ];

        // Add role-specific validation rules
        if ($this->role_id) {
            $role = \App\Models\Role::find($this->role_id);
            
            if ($role && $role->name === 'House Owner') {
                $rules['house_owner'] = 'required|array';
                $rules['house_owner.address'] = 'nullable|string|max:255';
                $rules['house_owner.city'] = 'nullable|string|max:255';
                $rules['house_owner.zip'] = 'nullable|string|max:10';
            } elseif ($role && $role->name === 'Tenant') {
                $rules['tenant'] = 'required|array';
                $rules['tenant.address'] = 'nullable|string|max:255';
                $rules['tenant.city'] = 'nullable|string|max:255';
                $rules['tenant.zip'] = 'nullable|string|max:10';
                $rules['tenant.house_owner_id'] = 'nullable|exists:house_owners,id';
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'Please select a role.',
            'role_id.exists' => 'The selected role is invalid.',
            'house_owner.required' => 'House owner information is required.',
            'tenant.required' => 'Tenant information is required.',
        ];
    }
}
