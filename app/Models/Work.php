<?php

namespace App\Models;

use App\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'video_url',
        'description',
        'contest_id',
        'user_id',
        'rank'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new CurrentTeam);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }


    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scores()
    {
        return $this->hasOne(Score::class);
    }

    public function details()
    {
        return $this->hasOne(WorkDetails::class);
    }
}
