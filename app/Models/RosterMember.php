<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RosterMember extends Model
{
    use HasFactory;
    protected $table = 'roster_members';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function bulkCreateRosterMembers($joinEventIds, $users) {
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'join_events_id' => $joinEventIds,
                'user_id' => $user->id,
                'status' => $user->status
            ];
        }

        return self::insert($data);
    }

}
