<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'link' => $this->link,
            'amount' => $this->amount,
            'summary' => $this->summary,
            'isPositive' => $this->isPositive,
            'date' => $this->date,
            'formatted_date' => $this->formatted_date,
            'formatted_time' => $this->formatted_time,
            'formatted_date_time' => $this->formatted_date_time,
        ];
    }

    
}
