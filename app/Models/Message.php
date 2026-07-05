<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'content', 'sent_at', 'is_synced' ];
 
    
protected $casts = [
        'is_synced' => 'boolean',
        'sent_at' => 'datetime',
    ];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function group()
{
    return $this->belongsTo(Group::class);
}

    public function exclusions()
    {
        return $this->hasMany(MessageExclusion::class);
    }

}
