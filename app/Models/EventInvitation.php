<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInvitation extends Model
{
    protected $table = 'event_invitations';

    use HasFactory;

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function team()
    {
        return $this->belongsTo(User::class, 'team_id');
    }

    public function event()
    {
        return $this->belongsTo(EventDetail::class, 'event_id');
    }

    public static function getParticipants($request, $teamId)
    {
        return self::query()
            ->where('role', 'PARTICIPANT')
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (! empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                    });
                }
            })
            ->with([
                'members' => function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                },
            ]);
    }
}
