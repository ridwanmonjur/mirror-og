<?php

namespace App\Models;

use App\Events\TeamMemberCreated;
use App\Events\TeamMemberUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'team_id', 'status', 'actor'];

    protected $dispatchesEvents = [
        'updating' => TeamMemberUpdated::class,
        'creating' => TeamMemberCreated::class,
    ];

    protected $table = 'team_members';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(ParticipantPayment::class, 'team_members_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
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

    public static function getProcessedTeamMembers($id)
    {
        $acceptedMembers = $pendingMembers = $rejectedMembers = $invitedMembers = $leftMembers = [];
        $acceptedMembersCount = $pendingMembersCount = $rejectedMembersCount = $invitedMemberCount = $leftMembersCount = 0;
        $members = self::where('team_id', $id)->with('user')->get();
        foreach ($members as $member) {
            $status = $member->status;
            if ($status == 'accepted') {
                $acceptedMembers[] = $member;
                $acceptedMembersCount++;
            } elseif ($status == 'pending') {
                $pendingMembers[] = $member;
                $pendingMembersCount++;
            } elseif ($status == 'rejected') {
                $rejectedMembers[] = $member;
                $rejectedMembersCount++;
            } elseif ($status == 'left') {
                $leftMembers[] = $member;
                $leftMembersCount++;
            } elseif ($status == 'invited') {
                $invitedMembers[] = $member;
                $invitedMemberCount++;
            }
        }

        return [
            'accepted' => [
                'count' => $acceptedMembersCount,
                'members' => $acceptedMembers,
            ],
            'pending' => [
                'count' => $pendingMembersCount,
                'members' => $pendingMembers,
            ],
            'rejected' => [
                'count' => $rejectedMembersCount,
                'members' => $rejectedMembers,
            ],

            'left' => [
                'count' => $leftMembersCount,
                'members' => $leftMembers,
            ],
        ];
    }

    public static function bulkCreateTeanMembers($teamId, $userIds, $status)
    {
        $data = [];

        foreach ($userIds as $userId) {
            $data[] = [
                'team_id' => $teamId,
                'user_id' => $userId,
                'status' => $status,
            ];
        }

        return self::insert($data);
    }
}
