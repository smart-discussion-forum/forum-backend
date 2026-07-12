<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'submitted_answer',
        'is_correct',
    ]; 
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id', 'Attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id', 'Question_id');
    }
}
