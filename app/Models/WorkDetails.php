<?php

namespace App\Models;

use App\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'team_id',
        'full_name', // Contestant Name
        'country',
        'county',
        'city',
        'phone', // Mentor Phone
        'mentor',
        'school',
        'school_director', // Optional (can be removed if not used)
        'year', // Year (Class)
        'age_group', // Age group (e.g., 6-11, 14-18)
        'type', // Type of artwork
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
