<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'full_name',
        'date_of_birth',
        'phone',
        'mentor',
        'school',
        'school_director',
    ];

    /**
     * Get the work that owns the details.
     */
    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
