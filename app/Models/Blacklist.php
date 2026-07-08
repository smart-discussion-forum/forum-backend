<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
      protected $fillable = [
        'User_id',
        'Reason',
        'Blacklisted_at',
        'Expires_at'
    ];

    public $timestamps = false;

    // A blacklist record belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }
}

