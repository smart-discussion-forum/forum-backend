<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'group_id', 'role', 'joined_at'];
    protected function casts():array
    {
        return [
            'joined_at'=>'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(Group::class);
    }
}
