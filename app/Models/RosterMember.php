<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class RosterMember extends Model
{
    use HasFactory;

    protected $table = 'roster_members';
    protected $fillable = ['user_id', 'join_events_id', 'team_id', 'team_member_id', ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function userJoinEventRoster(int| string $joinEventId, Collection $teamMembers, int|string $teamId, int|string $userId): bool
    {
        $data = [];

        foreach ($teamMembers as $member) {
            if ($member->status === 'accepted' && $member->user_id == $userId) {
                $data[] = [
                    'join_events_id' => $joinEventId,
                    'user_id' => $member->user_id,
                    'team_member_id' => $member->id,
                    'team_id' => $teamId
                ];
            }
        }

        return self::insert($data);
    }

    public static function getMembersByTeamIdList(array $teamIdList): Collection
    {
        return self::whereIn('join_events_id', $teamIdList)
            ->with('user')
            ->get();
    }

    public static function processEvents(Collection $members): array
    {
        $acceptedMembers = [];

        foreach ($members as $member) {
            $joinEventId = $member->join_event_id;
            $acceptedMembers[$joinEventId][] = $member;
        }

        return $acceptedMembers;
    }

    public static function keyByMemberId(Collection $rosterMembers): array
    {
        $associativeArray = [];
        foreach ($rosterMembers as $rosterMember) {
            $associativeArray[$rosterMember->team_member_id] = $rosterMember;
        }

        return $associativeArray;
    }

    public function countVotes(): array
    {
        $totalVoteCount = 0;
        $stayVoteCount = 0;
        $leaveVoteCount = 0;

        $totalVoteCount++;
        
        if ($this->vote_to_quit === true) {
            $leaveVoteCount++;
        } else {
            $stayVoteCount++;
        }

        return [$stayVoteCount, $leaveVoteCount, $totalVoteCount];
    }

    public function getRosterVoteView(
        string|int $userId, 
        array &$currentUser
    ): void {
        if ($this->user_id == $userId) {
            $currentUser['isUserPartOfRoster'] = true;
            $currentUser['memberId'] = $this->team_member_id;
            $currentUser['vote_to_quit'] = $this->vote_to_quit;
            $currentUser['rosterId'] = $this->id;

        }
        
    }
}
