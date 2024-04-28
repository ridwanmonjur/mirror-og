<?php

namespace App\Models;

use App\Events\TeamMemberUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;
    protected $dispatchesEvents = [
        'updated' => TeamMemberUpdated::class,
        'created' => TeamMemberUpdated::class,
    ];
    
    protected $table = 'team_members';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function isAlreadyMember($teamId, $userId)
    {
        return self::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->get();
    }

    public static function getMembersByTeamIdList($teamIdList)
    {
        return self::whereIn('team_id', $teamIdList)
            ->with('user')
            ->get();
    }

    public static function processStatus($members)
    {
        $acceptedMembers = $pendingMembers = $rejectedMembers = $invitedMembers = [];
        $acceptedMembersCount = $pendingMembersCount = $rejectedMembersCount = $invitedMemberCount = 0;
    
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
            } else if ($status == "invited") {
                $invitedMembers[] = $member;
                $invitedMemberCount++;
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
            ],
            'invited' => [
                'count' => $invitedMemberCount,
                'members' => $invitedMembers
            ],
        ];
    }

    public static function bulkCreateTeanMembers($teamId, $userIds, $status) {
        $data = [];

        foreach ($userIds as $userId) {
            $data[] = [
                'team_id' => $teamId,
                'user_id' => $userId,
                'status' => $status
            ];
        }

        return self::insert($data);
    }
}
