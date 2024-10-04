<?php

namespace App\Models;

use App\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'url',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CurrentTeam);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The contests that belong to the sponsor.
     */
    public function contests()
    {
        return $this->belongsToMany(Contest::class);
    }
}
