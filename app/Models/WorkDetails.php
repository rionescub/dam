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
        'full_name',
        'date_of_birth',
        'phone',
        'mentor',
        'school',
        'school_director',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CurrentTeam);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the work that owns the details.
     */
    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
