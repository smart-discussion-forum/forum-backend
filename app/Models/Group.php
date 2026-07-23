<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'Target_category', 'id');
    }
}
