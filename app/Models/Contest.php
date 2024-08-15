<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'parent_contest_id',
        'rules',
        'description',
        'user_id'
    ];

    public function parentContest()
    {
        return $this->belongsTo(Contest::class, 'parent_contest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }
}
