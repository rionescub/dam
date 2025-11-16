<?php

namespace App\Models;

use App\Scopes\ContestTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Work extends Model
{
    use HasFactory, SoftDeletes;

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
        'award_rank',
        'view_on_front'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'rank' => 'integer',
        'total_score' => 'float',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ContestTeam());
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
        return $this->hasMany(Score::class);
    }

    public function details()
    {
        return $this->hasOne(WorkDetails::class);
    }
}
