<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'group_id',
        'sender_id',
        'content',
        'sent_at',
        'is_synced',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'is_synced' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function exclusions()
    {
        return $this->hasMany(MessageExclusion::class);
    }
}
