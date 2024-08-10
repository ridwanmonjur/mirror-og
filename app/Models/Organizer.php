<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'companyName',
        'companyDescription',
        'industry',
        'type',
        'website_link',
        'instagram_link',
        'facebook_link',
        'twitter_link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
