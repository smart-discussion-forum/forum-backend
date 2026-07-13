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
    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }
}
