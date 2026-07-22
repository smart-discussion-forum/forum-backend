<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['group_id', 'created_by', 'title', 'category'];

    protected static function booted(): void
    {
        static::creating(function (Topic $topic) {
            $topic->category = $topic->classifyCategory($topic->title, $topic->category);
        });

        static::updating(function (Topic $topic) {
            if ($topic->isDirty(['title', 'category'])) {
                $topic->category = $topic->classifyCategory($topic->title, $topic->category);
            }
        });
    }

    public function classifyCategory(?string $title = null, ?string $category = null): string
    {
        $source = trim(($title ?? '') . ' ' . ($category ?? ''));
        $text = mb_strtolower($source);

        $normalizedCategory = strtolower(trim((string) $category));
        if (in_array($normalizedCategory, ['mathematics', 'programming', 'science', 'general'], true)) {
            return ucfirst($normalizedCategory);
        }

        if (preg_match('/\b(math|mathematics|calculus|algebra|geometry|trigonometry|statistics|equation|proof|derivative|integral)\b/', $text)) {
            return 'Mathematics';
        }

        if (preg_match('/\b(programming|code|php|laravel|javascript|python|api|developer|software|debug|algorithm|database)\b/', $text)) {
            return 'Programming';
        }

        if (preg_match('/\b(science|physics|chemistry|biology|lab|experiment|atom|cell|earth|astronomy|scientific)\b/', $text)) {
            return 'Science';
        }

        return 'General';
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
