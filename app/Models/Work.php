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
        'title_en',
        'description_en',
        'description',
        'image',
        'video_url',
        'file_path',
        'contest_id',
        'user_id',
        'rank',
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
