<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $primaryKey = 'Question_id';
    protected $fillable = [
        'Quiz_id',
        'Question',
        'Options',
        'Correct_answer',
        'Marks'
    ];

    public $timestamps = false;

    // A question belongs to a quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'Quiz_id');
    }
}
