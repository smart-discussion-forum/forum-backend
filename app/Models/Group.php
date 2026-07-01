<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'created_by'];

    public function creator()
    {
        return $this->belongsToMany(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
        ->withPivot('role', 'joined_at');
    }
    /*TODO: Topic model/migration doesn't exist yet (deleted on development, Jun 30).*/
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}

