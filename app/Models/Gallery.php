<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'contestant',
        'year',
        'team_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
