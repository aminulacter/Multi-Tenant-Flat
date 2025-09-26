<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuildingRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:buildings,name,' . $this->building->id,
            'house_owner_id' => 'required|exists:house_owners,id',
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Building name is required.',
            'name.max' => 'Building name must not exceed 255 characters.',
            'name.unique' => 'Building name already exists.',
            'house_owner_id.required' => 'House owner is required.',
            'house_owner_id.exists' => 'Selected house owner does not exist.',
            'address.max' => 'Address must not exceed 500 characters.',
        ];
    }
}
