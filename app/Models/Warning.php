<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model

    {
    protected $fillable = [
        'User_id',
        'Reason',
        'Issued_at'
    ];

    public $timestamps = false;

    // A warning belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }
}

