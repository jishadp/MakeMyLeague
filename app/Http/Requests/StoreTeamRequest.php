<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:teams,name',
            'home_ground_id' => 'nullable|exists:grounds,id',
            'local_body_id' => 'required|exists:local_bodies,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'league_slug' => 'nullable|exists:leagues,slug',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required.',
            'name.unique' => 'This team name is already taken.',
            'home_ground_id.exists' => 'Selected home ground is invalid.',
            'local_body_id.required' => 'Please select a local body.',
            'local_body_id.exists' => 'Selected local body is invalid.',
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a JPEG, PNG, JPG, or GIF file.',
            'logo.max' => 'The logo size must not exceed 2MB.',
            'banner.image' => 'The banner must be an image file.',
            'banner.mimes' => 'The banner must be a JPEG, PNG, JPG, or GIF file.',
            'banner.max' => 'The banner size must not exceed 5MB.',
        ];
    }
}
