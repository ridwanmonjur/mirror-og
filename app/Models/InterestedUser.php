<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterestedUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'email_verified_at',
        'email_verified_token',
        'pass_text'
    ];

    /**
     * Get the user associated with the interested record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

}
