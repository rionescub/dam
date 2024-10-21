<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'comments',
        'team_id', // Add team_id as fillable
    ];

    /**
     * Define the relationship with the Team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
