<?php

namespace App\Models;

use App\Events\TeamMemberCreated;
use App\Events\TeamMemberUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'team_id', 'status', 'actor'];

    protected $dispatchesEvents = [
        'updating' => TeamMemberUpdated::class,
        'creating' => TeamMemberCreated::class,
    ];

    protected $table = 'team_members';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
    public function payments(): HasMany
    {
        return $this->hasMany(ParticipantPayment::class, 'team_members_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public static function isAlreadyMember(int| string $teamId, int| string $userId)
    {
        return self::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->get();
    }

    public static function getMembersByTeamIdList(array $teamIdList)
    {
        return self::whereIn('team_id', $teamIdList)
            ->with('user')
            ->get();
    }

    public static function getProcessedTeamMembers(string| int $id): array
    {
        $acceptedMembers = $pendingMembers = $rejectedMembers = $leftMembers = collect();
        $acceptedMembersCount = $pendingMembersCount = $rejectedMembersCount  = $leftMembersCount = 0;
        $members = self::where('team_id', $id)->with('user')->get();
        foreach ($members as $member) {
            $status = $member->status;
            if ($status === 'accepted') {
                $acceptedMembers[] = $member;
                $acceptedMembersCount++;
            } elseif ($status === 'pending') {
                $pendingMembers[] = $member;
                $pendingMembersCount++;
            } elseif ($status === 'rejected') {
                $rejectedMembers[] = $member;
                $rejectedMembersCount++;
            } elseif ($status === 'left') {
                $leftMembers[] = $member;
                $leftMembersCount++;
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

    public static function bulkCreateTeanMembers(string| int $teamId, array $userIds, string $status): bool
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
