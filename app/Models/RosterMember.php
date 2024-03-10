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

    public static function bulkCreateRosterMembers($joinEventIds, $teamMembers) {
        $data = [];

        foreach ($teamMembers as $key=>$member) {
            if ($member->status == 'accepted') {
                $data[] = [
                    'join_events_id' => $joinEventIds[$key],
                    'user_id' => $member->user_id,
                    'team_member_id' => $member->id,
                    'status' => $member->status
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

    public static function processStatus($members)
    {
        $acceptedMembers = $pendingMembers = $rejectedMembers = [];
        $acceptedMembersCount = $pendingMembersCount = $rejectedMembersCount = 0;
    
        foreach ($members as $member) {
            $status = $member->status;
    
            if ($status == "accepted") {
                $acceptedMembers[] = $member;
                $acceptedMembersCount++;
            } else if ($status == "pending") {
                $pendingMembers[] = $member;
                $pendingMembersCount++;
            } else if ($status == "rejected") {
                $rejectedMembers[] = $member;
                $rejectedMembersCount++;
            }
        }

        return [
            'accepted' => [
                'count' => $acceptedMembersCount,
                'members' => $acceptedMembers
            ],
            'pending' => [
                'count' => $pendingMembersCount,
                'members' => $pendingMembers
            ],
            'rejected' => [
                'count' => $rejectedMembersCount,
                'members' => $rejectedMembers
            ]
        ];
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

}
