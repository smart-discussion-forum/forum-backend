<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $primaryKey = 'Attempt_id';

    protected $fillable = [
        'quiz_id',
        'Student_id',
        'Score',
        'Auto_submitted'
    ];

    public $timestamps = false;

    public function getIdAttribute()
    {
        return $this->Attempt_id;
    }

    public function getScoreAttribute()
    {
        return $this->attributes['Score'] ?? null;
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'Student_id');
    }
}