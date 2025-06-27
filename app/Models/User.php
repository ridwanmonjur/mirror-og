<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;

    protected $perPage = 5;


    public function canAccessPanel($panel): bool
    {
        return $this->role == 'ADMIN';
    }

    public $timestamps = false;

    public function createdAtDiffForHumans() {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function updatedAtDiffForHumans() {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    public function createdIsoFormat() {
        return Carbon::parse($this->created_at)->tz('Asia/Kuala_Lumpur')->isoFormat('Do MMMM YYYY');
    }
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'demo_email', 'password', 'role', 'mobile_no', 'created_at', 'updated_at',
        'email_verified_at',  'email_verified_token', 'recovery_email'
    ];

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

    public function notificationCount(): HasOne
    {
        return $this->hasOne(NotificationCounter::class, 'user_id');
    }

    public function notificationList(): HasMany
    {
        return $this->hasMany(NotifcationsUser::class, 'user_id');
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

    public function stars(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stars', 'user_id', 'starred_user_id')
            ->withTimestamps();
    }

    public function starredBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stars', 'starred_user_id', 'user_id')
            ->withTimestamps();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'blocks', 'user_id', 'blocked_user_id')
            ->withTimestamps();
    }

    public function hasStarred(User $user): bool
    {
        return $this->stars()->where('starred_user_id', $user->id)->exists();
    }

    public function hasBlocked(User $user): bool
    {
        return $this->blocks()->where('blocked_user_id', $user->id)->exists();
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
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
            // 'region' => 'participants.region_name',
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
                        $q->orWhere('name', 'LIKE', "%{$search}%");
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
                    $query->where('region_name', $region);
                }

                if (! empty($birthDate)) {
                    $query->whereDate('birthday', '<', $birthDate);
                }
            })
            ->with([
                'participant' => function ($query) {
                    $query->select([
                        'region_flag',
                        'region_name',
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
    $oldBanner = $this->userBanner;
    $newBannerPath = null;
    
    try {
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['file'])) {
            return null;
        }

        $fileData = $requestData['file'];
        $fileContent = base64_decode($fileData['content']);
        
        $fileNameInitial = 'userBanner-'.time().'.'.pathinfo($fileData['filename'], PATHINFO_EXTENSION);
        $fileName = "images/user/{$fileNameInitial}";
        $storagePath = storage_path('app/public/'.$fileName);
        
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }

        if (file_put_contents($storagePath, $fileContent) === false) {
            throw new \Exception('Failed to save file');
        }

        $newBannerPath = $fileName;
        
        $this->userBanner = $fileName;
        $this->save();

        $this->destroyUserBanner($oldBanner);

        return asset('storage/'.$fileName);

    } catch (\Exception $e) {
        if ($newBannerPath && file_exists(storage_path('app/public/'.$newBannerPath))) {
            unlink(storage_path('app/public/'.$newBannerPath));
        }
        
        $this->userBanner = $oldBanner;
        $this->save();
        throw $e; 
    }
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

    public function slugify () {
        $this->slug = Str::slug($this->name);
    }
}
