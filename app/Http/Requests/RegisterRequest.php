<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'country_code' => 'required|string|max:10',
            'mobile' => 'required|string|max:10|min:10|unique:users|regex:/^[0-9]{10}$/',
            'pin' => 'required|string|min:4|max:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'country_code.required' => 'Please select your country code.',
            'mobile.required' => 'Please enter your mobile number.',
            'mobile.min' => 'Mobile number must be exactly 10 digits.',
            'mobile.max' => 'Mobile number must be exactly 10 digits.',
            'mobile.unique' => 'This mobile number is already registered.',
            'pin.required' => 'Please enter your PIN.',
            'pin.min' => 'PIN must be at least 4 digits.',
            'pin.max' => 'PIN cannot exceed 6 digits.',
        ];
    }
}
