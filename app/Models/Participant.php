<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function getRegionDetails()
    {
        return Country::select('emoji_flag', 'name', 'id')
            ->find($this->region);
    }

    public function getRegionDetailsFromRegionIdList($regionIdList)
    {
        return Country::select('emoji_flag', 'name', 'id')
            ->findOrFail($regionIdList)
            ->keyBy('id')
            ->map(function ($item) {
                return $item;
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
