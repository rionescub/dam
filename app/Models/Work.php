<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
