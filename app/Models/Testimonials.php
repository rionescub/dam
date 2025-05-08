<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CurrentTeam;

class Testimonials extends Model
{
    protected $fillable = [
        'name',
        'location',
        'rating',
        'text'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'rating' => 'required|integer|between:1,5'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CurrentTeam);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
