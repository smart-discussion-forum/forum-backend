<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'blacklist';
      protected $primaryKey = 'Blacklist_id';

      protected $fillable = [
        'User_id',
        'Reason',
        'Blacklisted_at',
        'Expires_at'
    ];

    protected $casts = [
        'Blacklisted_at' => 'datetime',
        'Expires_at' => 'datetime',
    ];

    public $timestamps = false;

    // A blacklist record belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    // True while this entry has no expiry, or its expiry is still in the future.
    public function isActive(): bool
    {
        return $this->Expires_at === null || $this->Expires_at->isFuture();
    }
}

