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
            'email' => 'nullable|email|max:100|unique:users',
            'mobile' => 'required|string|max:10|min:10|unique:users',
            'pin' => 'required|string|min:4|max:6',
            'position_id' => 'nullable|exists:game_positions,id',
            'local_body_id' => 'nullable|exists:local_bodies,id'
        ];
    }
}
