<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['group_id', 'created_by', 'title', 'category'];

   
   public function group()
    {
        return $this->belongsTo(Group::class);
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
