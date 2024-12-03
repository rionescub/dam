<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Scopes\CurrentTeam;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'email_verified_at',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }



    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // add the relationships
    public function contests()
    {
        return $this->belongsToMany(Contest::class);
    }

    public function createdContests()
    {
        return $this->hasMany(Contest::class, 'user_id');
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function diplomas()
    {
        return $this->hasMany(Diploma::class);
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }



    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function is_super_admin()
    {
        return $this->role === 'superadmin';
    }

    public function is_admin()
    {
        return $this->role === 'admin';
    }

    public function is_organizer()
    {
        return $this->role === 'organizer' || $this->role === 'admin';
    }
}
