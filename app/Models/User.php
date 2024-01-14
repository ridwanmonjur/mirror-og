<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
   
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
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
        return $this->hasOne(Team::class, 'user_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'members');
    }

    public function isFollowing(Organizer $organizer)
    {
        if (is_null($organizer)) return null;
        return $this->following()->where('organizer_id', $organizer->id)->exists();
    }

    public function following()
    {
        return $this->belongsToMany(Organizer::class, 'follows', 'user_id', 'organizer_id');
    }
    
}