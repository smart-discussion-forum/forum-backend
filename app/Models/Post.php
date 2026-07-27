<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['topic_id','user_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function getShareUrlAttribute()
    {
        return url('/posts/'.$this->id.'/share');
    }

    // Short, safe preview text for og:description and share text —
    // never leak the full post content publicly, just a teaser.
    public function getShareExcerptAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 120);
    }
}
