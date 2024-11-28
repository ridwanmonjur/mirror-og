<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class DisapproveMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'user_id' => 'required',
            'join_events_id' => 'required',
            'team_id' => 'required',
        ];
    }
}
