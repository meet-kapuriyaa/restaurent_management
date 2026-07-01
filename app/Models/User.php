<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user's role has permission for a specific page or action.
     */
    public function hasPermission(string $action): bool
    {
        if ($this->role === 'admin') {
            return true;
        }
        return \App\Models\RolePermission::where('role', $this->role)
            ->where('page', $action)
            ->where('is_allowed', true)
            ->exists();
    }

    /**
     * Get user avatar path based on their role.
     */
    public function getAvatarAttribute()
    {
        $role = $this->role;
        if ($role === 'admin') {
            return '/avatar-admin.png';
        }
        if ($role === 'waiter') {
            return '/avatar-waiter.png';
        }
        if ($role === 'chef') {
            return '/avatar-chef.png';
        }
        if ($role === 'manager') {
            return '/avatar-manager.png';
        }
        return '/avatar.png';
    }
}
