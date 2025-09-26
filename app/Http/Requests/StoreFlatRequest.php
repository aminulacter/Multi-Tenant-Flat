<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlatRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'building_id' => 'required|exists:buildings,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'house_owner_id' => 'nullable|exists:house_owners,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Flat name is required.',
            'name.string' => 'Flat name must be a string.',
            'name.max' => 'Flat name may not be greater than 255 characters.',
            'building_id.required' => 'Building is required.',
            'building_id.exists' => 'Selected building does not exist.',
            'tenant_id.exists' => 'Selected tenant does not exist.',
            'house_owner_id.exists' => 'Selected house owner does not exist.',
        ];
    }
}
