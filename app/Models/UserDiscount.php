<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDiscount extends Model
{
    use HasFactory;

    protected $table = 'user_discounts';
    protected $fillable = [
        'amount',
        'user_id'
    ];


    /**
     * Get the user that owns the discount
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
