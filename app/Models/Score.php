<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_id',
        'creativity_score',
        'link_score',
        'aesthetic_score',
        'total_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->total_score = $model->creativity_score + $model->link_score + $model->aesthetic_score;
        });
    }
}
