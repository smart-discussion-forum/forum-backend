<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $primaryKey = 'Attempt_id';
    protected $fillable = [
        'Quiz_id',
        'Student_id',
        'Score',
        'Auto_submitted'
    ];

    public $timestamps = false;

    // An attempt belongs to a quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    // An attempt belongs to a student (who is a user)
    public function student()
    {
        return $this->belongsTo(User::class, 'Student_id');
    } 

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id', 'Attempt_id');
    }


}
