<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipationMark extends Model
{
    protected $fillable = ['user_id', 'group_id', 'score' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
