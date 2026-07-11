<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Quiz extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'Lecturer_id',
        'Title',
        'Target_category',
        'Publish_time',
        'Duration'
    ];

    public function getIdAttribute()
    {
        return $this->quiz_id;
    }

    public function getTitleAttribute()
    {
        return $this->attributes['Title'] ?? null;
    }

    public function getTargetCategoryAttribute()
    {
        return $this->attributes['Target_category'] ?? null;
    }

    public function getStartTimeAttribute()
    {
        return isset($this->attributes['Publish_time'])
            ? Carbon::parse($this->attributes['Publish_time'])
            : null;
    }

    public function getEndTimeAttribute()
    {
        if (!isset($this->attributes['Publish_time']) || !isset($this->attributes['Duration'])) {
            return null;
        }
        return Carbon::parse($this->attributes['Publish_time'])->addMinutes((int) $this->attributes['Duration']);
    }

    public function getAnnouncedAtAttribute()
    {
        return Cache::get('quiz_announced_' . $this->quiz_id);
    }

    public function markAnnounced()
    {
        Cache::put('quiz_announced_' . $this->quiz_id, now(), now()->addYear());
    }

    public function getStatusAttribute()
    {
        $now = now();
        $start = $this->start_time;
        $end = $this->end_time;

        if (!$start || !$end) return 'unknown';
        if ($now->lt($start)) return 'upcoming';
        if ($now->between($start, $end)) return 'active';
        return 'closed';
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'Lecturer_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id', 'quiz_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id', 'quiz_id');
    }
}