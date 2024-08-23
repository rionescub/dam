<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
}
