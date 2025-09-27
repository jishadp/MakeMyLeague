<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFixturesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'format' => 'required|in:single_round_robin,double_round_robin'
        ];
    }

    public function messages()
    {
        return [
            'format.required' => 'Tournament format is required.',
            'format.in' => 'Invalid tournament format selected.'
        ];
    }
}