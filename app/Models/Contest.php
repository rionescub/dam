<?php

namespace App\Models;

use App\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'phase',
        'parent_contest_id',
        'rules',
        'description',
        'user_id',
        'team_id', // Ensure this is fillable so the correct team can be assigned
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'jury_date' => 'datetime',
            'ceremony_date' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new CurrentTeam);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function parentContest()
    {
        return $this->belongsTo(Contest::class, 'parent_contest_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class);
    }
}
