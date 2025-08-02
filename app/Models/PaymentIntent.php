<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIntent extends Model
{
    use HasFactory;

    protected $table = 'payment_intents';

    protected $fillable = ['user_id', 'payment_intent_id', 'customer_id', 'status', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
