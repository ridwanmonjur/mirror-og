<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

// use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable 
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

    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'user_id', 'id');
    }

    public function organizer(): HasOne
    {
        return $this->hasOne(Organizer::class, 'user_id');
    }

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id');
    }

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'creator_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'user_id');
    }


    public function follows(): HasMany
    {
        return $this->hasMany(OrganizerFollow::class,  'organizer_user_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLogs::class, 'subject');
    }

    public static function getParticipants(Request $request): Builder
    {
        $teamId = $request->input('teamId');
        $status = $request->input('status');
        $search = trim($request->input('search'));
        $region = trim($request->input('region'));
        $birthDate = $request->input('birthDate');
        $sortType = trim($request->input('sortType'));
        $sortKeys = trim($request->input('sortKeys'));

        if (empty($sortKeys) || empty($sortType)) {
            $sortType = 'asc';
            $sortKeys = 'recent';
        }

        $mapSortKeysToValues = [
            'name' => 'name',
            'created_at' => 'created_at',
            // 'region' => 'participants.region',
            // 'birthDate' => 'participants.birthday',
            'recent' => 'id',
        ];

        $sortColumn = $mapSortKeysToValues[$sortKeys] ?? 'id';

        return self::query()
            ->where('role', 'PARTICIPANT')
            ->select([
                'id',
                'email',
                'role',
                'userBanner',
                'name',
            ])
            ->where(function ($query) use ($search, $status, $teamId) {
                if (! empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                }

                $countStatus = count($status);
                if ($countStatus !== 0 && $countStatus !== 4) {
                    $query->whereHas('members', function ($q) use ($teamId, $status) {
                        $q->where('team_id', $teamId)
                            ->whereIn('status', $status);
                    });
                }
            })
            ->whereHas('participant', function ($query) use ($region, $birthDate) {
                if (! empty($region)) {
                    $query->where('region', $region);
                }

                if (! empty($birthDate)) {
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
                'members' => function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                },
            ])
            ->orderBy($sortColumn, $sortType);
    }

    public function uploadUserBanner(Request $request): ?string
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

    public function uploadBackgroundBanner(Request $request, UserProfile | TeamProfile $profile): ?string
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

    public function destroyUserBanner(string| null $fileName): void
    {
        if ($fileName) {
            if (Storage::disk('public')->exists($fileName)) {
                Storage::disk('public')->delete($fileName);
            }
            // dd("File does not exist");
        }
    }
}
