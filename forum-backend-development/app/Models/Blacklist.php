<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'blacklist';
    protected $primaryKey = 'Blacklist_id';
    public $timestamps = false;

    protected $fillable = ['User_id', 'Reason', 'Blacklisted_at', 'Expires_at'];

    protected $casts = [
        'Blacklisted_at' => 'datetime',
        'Expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    /**
     * A blacklist entry is still in force if it has no expiry,
     * or its expiry date is in the future.
     */
    public function isActive(): bool
    {
        return is_null($this->Expires_at) || $this->Expires_at->isFuture();
    }
}
