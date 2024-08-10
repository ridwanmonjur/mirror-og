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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function bulkCreateRosterMembers(int| string $joinEventId, Collection $teamMembers): bool
    {
        $data = [];

        foreach ($teamMembers as $member) {
            if ($member->status === 'accepted') {
                $data[] = [
                    'join_events_id' => $joinEventId,
                    'user_id' => $member->user_id,
                    'team_member_id' => $member->id,
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
}
