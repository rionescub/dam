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


    /**
     * The contests that belong to the sponsor.
     */
    public function contests()
    {
        return $this->belongsToMany(Contest::class);
    }
}
