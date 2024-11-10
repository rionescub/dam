<?php

namespace App\Models;

use App\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diploma extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contest_id',
        'work_id',
        'score_id',
        'description'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function score()
    {
        return $this->belongsTo(Score::class);
    }
}
