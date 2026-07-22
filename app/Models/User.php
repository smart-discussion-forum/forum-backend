<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
Use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens ,HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password','role', 'status','last_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'role' => RoleEnum::class,
            'status' => StatusEnum::class,
            'last_active' => 'datetime',
        ];
    }

    public function groups(){
        return $this->belongsToMany(Group::class, 'group_members')
        ->withPivot('role', 'joined_at');

    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
    public function sentMessages()
{
    return $this->hasMany(Message::class,'sender_id');
}

    public function warnings()
    {
        return $this->hasMany(Warning::class, 'User_id');
    }

    public function blacklistEntries()
    {
        return $this->hasMany(Blacklist::class, 'User_id');
    }
}
