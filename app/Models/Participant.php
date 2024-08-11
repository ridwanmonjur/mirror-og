<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Io238\ISOCountries\Models\Country;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'birthday', 'user_id', 'domain', 'age', 'bio', 'nickname', 'region',
        'region_name', 'region_flag', 'games_data', 'isAgeVisible',
    ];

    protected $casts = [
        'games_data' => 'array',
    ];

    public function getRegionDetails(): ? Country
    {
        return Country::select('emoji_flag', 'name', 'id')
            ->find($this->region);
    }

    public function getRegionDetailsFromRegionIdList($regionIdList): Collection
    {
        return Country::select('emoji_flag', 'name', 'id')
            ->findOrFail($regionIdList)
            ->keyBy('id')
            ->map(function ($item) {
                return $item;
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
