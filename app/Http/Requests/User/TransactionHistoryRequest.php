<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class TransactionHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cursor' => 'nullable|string',
            'type' => 'nullable|string',
        ];
    }

    public function getCursor(): ?string
    {
        return $this->get('cursor');
    }


    public function getType(): ?string
    {
        return $this->get('type');
    }


}