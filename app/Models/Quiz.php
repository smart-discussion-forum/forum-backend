<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'quiz_id';
     protected $fillable = [
        'lecturer_id',
        'Title',
        'Target_category',
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
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    // A quiz has many attempts
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}
