<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stars extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'starred_user_id',
    ];

    protected $table = 'stars';

    /**
     * Get the user who created the star
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who was starred
     */
    public function starredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'starred_user_id');
    }
}
