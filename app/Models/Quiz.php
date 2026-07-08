<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public $timestamps = false;
     protected $fillable = [
        'lecturer_id',
        'title',
        'target_category',
        'Publish_time',
        'Duration'
    ];



    // A quiz belongs to a lecturer (who is a user)
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // A quiz has many questions
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'Quiz_id');
    }

    // A quiz has many attempts
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'Quiz_id');
    }
}
