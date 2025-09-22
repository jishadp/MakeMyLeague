<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'groups' => 'required|array|min:2',
            'groups.*.name' => 'required|string|max:255',
            'groups.*.team_ids' => 'required|array|min:1',
            'groups.*.team_ids.*' => 'exists:league_teams,id'
        ];
    }

    public function messages()
    {
        return [
            'groups.min' => 'At least 2 groups are required.',
            'groups.*.name.required' => 'Group name is required.',
            'groups.*.team_ids.required' => 'Each group must have at least one team.',
            'groups.*.team_ids.*.exists' => 'Invalid team selected.'
        ];
    }
}