<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, \App\Traits\GeneratesCustomIds;

    const ID_PREFIX = 'ID';

    protected $fillable = [
        'id', 'name', 'email', 'username', 'password',
        'role', 'avatar_url', 'is_active',
        'google_id', 'google_token', 'google_refresh_token',
    ];


    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'is_active'         => 'boolean',
    ];

    /** Scope to only fetch active (non-deactivated) users */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function cases()
    {
        return $this->hasMany(NotarisCase::class, 'created_by');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    /** Helper: check if user has admin-level privileges */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'notaris']);
    }

    /** Helper: check role */
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    /**
     * Get the user's avatar URL dynamically.
     * Rewrites local storage URLs to match the current request host and subfolder
     * to prevent broken images when switching environments or tunnels (e.g., ngrok).
     */
    public function getAvatarUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http') && str_contains($value, '/storage/avatars/')) {
            $filename = basename(parse_url($value, PHP_URL_PATH));
            return url('/storage/avatars/' . $filename);
        }

        if (str_starts_with($value, 'http') && str_contains($value, '/uploads/avatars/')) {
            $filename = basename(parse_url($value, PHP_URL_PATH));
            return url('/uploads/avatars/' . $filename);
        }

        if (!str_starts_with($value, 'http')) {
            return url($value);
        }

        return $value;
    }
}
