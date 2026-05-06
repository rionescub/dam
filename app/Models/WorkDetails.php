<?php

namespace App\Models;

use App\Scopes\WorkTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDetails extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'work_id',
        'team_id',
        'full_name',
        'country',
        'county',
        'city',
        'phone',
        'mentor',
        'school',
        'school_address',
        'school_director',
        'year',
        'age_group',
        'type',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkTeam());
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
