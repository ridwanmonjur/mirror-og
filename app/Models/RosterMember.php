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

    public static function bulkCreateRosterMembers($joinEventId, $teamMembers) {
        $data = [];

        foreach ($teamMembers as $member) {
            if ($member->status == 'accepted') {
                $data[] = [
                    'join_events_id' => $joinEventId,
                    'user_id' => $member->user_id,
                    'team_member_id' => $member->id,
                ];
            }
        }

        return self::insert($data);
    }

    public static function getMembersByTeamIdList($teamIdList)
    {
        return self::whereIn('join_events_id', $teamIdList)
            ->with('user')
            ->get();
    }


    public static function processEvents($members)
    {
        $acceptedMembers = [];

        foreach ($members as $member) {
            $joinEventId = $member->join_event_id;
            $acceptedMembers[$joinEventId][] = $member;
        }

        return $acceptedMembers;
    }

    public static function keyByMemberId($rosterMembers) {
        $associativeArray = [];
        foreach ($rosterMembers as $rosterMember) {
            $associativeArray[$rosterMember->team_member_id] = $rosterMember;
        }

        return $associativeArray;
    }

}
