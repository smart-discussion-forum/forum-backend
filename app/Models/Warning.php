<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_AUTO_INACTIVITY = 'auto_inactivity';

    protected $table = 'warnings';
    protected $primaryKey = 'Warning_id';
    public $timestamps = false;

    protected $fillable = ['User_id', 'Reason', 'Issued_at', 'Source'];

    protected $casts = [
        'Issued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('User_id', $userId);
    }

    public function scopeAutoInactivity($query)
    {
        return $query->where('Source', self::SOURCE_AUTO_INACTIVITY);
    }

    public function scopeManual($query)
    {
        return $query->where('Source', self::SOURCE_MANUAL);
    }
}
