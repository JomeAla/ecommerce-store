<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function isSupport(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'support']);
    }

    public function getLastLoginAgoAttribute(): ?string
    {
        if (!$this->last_login_at) {
            return null;
        }

        return $this->last_login_at->diffForHumans();
    }

    public function getAvatarAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = array_map(fn($word) => strtoupper(substr($word, 0, 1)), $words);
        return implode('', array_slice($initials, 0, 2));
    }
}