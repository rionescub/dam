<?php

namespace App\Models;

use App\Scopes\MyTeams;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;
    protected static function booted()
    {
        static::addGlobalScope(new MyTeams);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
