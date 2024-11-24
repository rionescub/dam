<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
