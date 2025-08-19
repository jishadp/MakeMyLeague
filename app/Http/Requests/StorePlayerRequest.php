<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15|unique:users,mobile',
            'pin' => 'required|string|max:10',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'position_id' => 'required|exists:game_positions,id',
            'local_body_id' => 'nullable|exists:local_bodies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'league_slug' => 'nullable|exists:leagues,slug',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Player name is required.',
            'mobile.required' => 'Mobile number is required.',
            'mobile.unique' => 'This mobile number is already registered.',
            'pin.required' => 'PIN is required.',
            'email.unique' => 'This email address is already registered.',
            'position_id.required' => 'Please select a player position.',
            'position_id.exists' => 'Selected position is invalid.',
            'local_body_id.exists' => 'Selected local body is invalid.',
            'photo.image' => 'The photo must be an image file.',
            'photo.mimes' => 'The photo must be a JPEG, PNG, JPG, or GIF file.',
            'photo.max' => 'The photo size must not exceed 2MB.',
        ];
    }
}
