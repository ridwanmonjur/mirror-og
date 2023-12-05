<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'eventTags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tier()
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }

    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function game()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }
    
    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
