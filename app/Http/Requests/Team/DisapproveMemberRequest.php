<?php

namespace App\Http\Requests\Team;

use App\Models\JoinEvent;
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
            'join_events_id' => function ($attribute, $value, $fail) {
                $joinEvent = JoinEvent::find($value);
                if (! $joinEvent) {
                    $fail('The selected join event is missing.');
                }

                if ($joinEvent && $joinEvent->join_status != 'pending') {
                    $fail('The selected join event is now locked.');
                }
            },
            'team_id' => 'required',
        ];
    }
}
