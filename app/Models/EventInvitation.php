<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class EventInvitation extends Model
{
    use HasFactory;

    protected $table = 'event_invitations';

    protected $fillable = ['organizer_user_id', 'participant_user_id', 'team_id', 'event_id'];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }

    public static function getParticipants(Request $request, int| string $teamId): Builder
    {
        return self::query()
            ->where('role', 'PARTICIPANT')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (! empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('name', 'LIKE', "%{$search}%");
                    });
                }
            });
    }
}
