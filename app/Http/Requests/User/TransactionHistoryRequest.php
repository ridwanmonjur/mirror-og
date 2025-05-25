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
            'page' => 'nullable|string',
        ];
    }

    public function getCursor(): ?string
    {
        return $this->get('cursor', null);
    }

    public function getPerPage(): ?string
    {
        return $this->get('page', 2);
    }


   

}