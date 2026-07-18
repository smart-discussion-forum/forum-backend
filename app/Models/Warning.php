<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model

    {
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_AUTO_INACTIVITY = 'auto_inactivity';

    protected $primaryKey = 'Warning_id';

    protected $fillable = [
        'User_id',
        'Reason',
        'Issued_at',
        'Source',
    ];

    public $timestamps = false;

    // A warning belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    // Scope: warnings belonging to a given user id
    public function scopeForUser($query, $userId)
    {
        return $query->where('User_id', $userId);
    }

    // Scope: only manually-issued (Lecturer/Admin) warnings
    public function scopeManual($query)
    {
        return $query->where('Source', self::SOURCE_MANUAL);
    }

    // Scope: only automatic inactivity warnings
    public function scopeAutoInactivity($query)
    {
        return $query->where('Source', self::SOURCE_AUTO_INACTIVITY);
    }
}

