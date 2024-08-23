<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
