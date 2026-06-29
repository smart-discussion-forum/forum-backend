<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['lecturer_id', 'title', 'questions', 'start_time', 'duration_minutes', 'target_category', 'status'];

    protected $casts = [
        'questions' => 'array',
        'start_time' => 'datetime',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function getEndTimeAttribute()
    {
        return $this->start_time->copy()->addMinutes($this->duration_minutes);
    }
}
