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

    public function canAccessFilament(): bool
    {
        return $this->role === 'ADMIN';
    }

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

    public static function getParticipants($request)
    {
        return self::query()
            ->where('role', 'PARTICIPANT')
            ->select([
                'id',
                'email',
                'role',
                'userBanner',
                'name',
            ])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (! empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                    });
                }
            })
            ->whereHas('participant', function ($query) use ($request) {
                $region = trim($request->input('region'));
                $birthDate = $request->input('birthDate');
            
                if (!empty($region)) {
                    $query->where('region', $region);
                }
            
                if (!empty($birthDate)) {
                    $query->whereDate('birthday', '<', $birthDate);
                }
            })
            ->with([
                'participant' => function ($query) {
                    $query->select([
                        'region_flag',
                        'region',
                        'birthday',
                        'user_id',
                    ]);
                },
                'members' => function ($query) use ($request) {
                    $query->where('team_id', $request->teamId);
                },
            ]);
    }

    public function uploadUserBanner($request)
    {
        $requestData = json_decode($request->getContent(), true);
        $fileData = $requestData['file'];

        $fileContent = base64_decode($fileData['content']);
        $fileNameInitial = 'userBackground-'.time().'.'.pathinfo($fileData['filename'], PATHINFO_EXTENSION);
        $fileName = "images/user/{$fileNameInitial}";
        $storagePath = storage_path('app/public/'.$fileName);
        file_put_contents($storagePath, $fileContent);

        $this->userBanner = $fileName;
        $this->save();

        return asset('storage/'.$fileName);
    }

    public function uploadBackgroundBanner($request, $profile)
    {
        $file = $request->file('backgroundBanner');
        $fileNameInitial = 'userBanner-'.time().'.'.$file->getClientOriginalExtension();
        $fileName = "images/user/{$fileNameInitial}";
        $file->storeAs('images/user/', $fileNameInitial);
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
            }
            // dd("File does not exist");
        }
    }
}
