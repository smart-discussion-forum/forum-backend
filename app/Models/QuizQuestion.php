<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $primaryKey = 'Question_id';

    protected $fillable = [
        'quiz_id',
        'Question',
        'Options',
        'Correct_answer',
        'Marks'
    ];

    public $timestamps = false;

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    public function getOptionsArrayAttribute()
    {
        $decoded = json_decode($this->attributes['Options'] ?? '[]', true);
        return is_array($decoded) ? $decoded : [];
    }
}