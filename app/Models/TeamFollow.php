<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamFollow extends Model {
    use HasFactory;

    protected $table = 'team_follows';

    protected $fillable = [
        'user_id',
        'team_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getFollowersPaginate($teamId, $perPage, $page = 1, $search = null)
    {
        return self::where('team_id', $teamId)
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'userBanner', 'created_at', 'role');
            }])
            ->when($search, function($query) use ($search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })
            ->simplePaginate($perPage, ['*'], 'team_followers_page', $page)
            ->through(function ($follow) {
                return [
                    'id' => $follow->user->id,
                    'name' => $follow->user->name,
                    'email' => $follow->user->email,
                    'userBanner' => $follow->user->userBanner,
                    'created_at' => $follow->created_at,
                    'role' => $follow->user->role,
                ];
            });
    }
}