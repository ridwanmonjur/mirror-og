<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

// use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    public function canAccessFilament(): bool
    {
        return $this->role == 'ADMIN';
    }

    public static $filamentUserColumn = 'is_filament_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password', 'role', 'mobile_no', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function address()
    {
        return $this->hasOne(Address::class, 'user_id', 'id');
    }

    public function organizer()
    {
        return $this->hasOne(Organizer::class, 'user_id');
    }

    public function participant()
    {
        return $this->hasOne(Participant::class, 'user_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'creator_id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'organizer_follows', 'user_id', 'organizer_id');
    }

    public function follows()
    {
        return $this->hasMany(OrganizerFollow::class, 'user_id');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLogs::class, 'subject');
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

    public function uploadUserBanner($request)
    {
        $requestData = json_decode($request->getContent(), true);
        $fileData = $requestData['file'];

        $fileContent = base64_decode($fileData['content']);
        $fileNameInitial = 'userBackground-'.time().'.'.pathinfo($fileData['filename'], PATHINFO_EXTENSION);
        $fileName = "images/user/$fileNameInitial";
        $storagePath = storage_path('app/public/'.$fileName);
        file_put_contents($storagePath, $fileContent);

        $this->userBanner = $fileName;
        $this->save();

        return asset('storage/'.$fileName);
    }

    public function uploadBackgroundBanner($request, $profile)
    {
        $requestData = json_decode($request->getContent(), true);
        $fileData = $requestData['backgroundBanner'];

        $fileContent = base64_decode($fileData['content']);
        $fileNameInitial = 'userBanner-'.time().'.'.pathinfo($fileData['filename'], PATHINFO_EXTENSION);
        $fileName = "images/user/$fileNameInitial";
        $storagePath = storage_path('app/public/'.$fileName);
        file_put_contents($storagePath, $fileContent);
        $profile->backgroundBanner = $fileName;
        $profile->backgroundColor = null;
        $profile->backgroundGradient = null;
        $profile->save();

        return asset('storage/'.$fileName);
    }

    public function destroyUserBanner($fileName)
    {
        if ($fileName) {
            if (Storage::disk('public')->exists($fileName)) {
                Storage::disk('public')->delete($fileName);
            } else {
                // dd("File does not exist");
            }
        } else {
        }
    }
}
