<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'photo_path',
        'roles', // foreign key ke tabel roles.id
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
     * Relasi ke Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roles'); 
        // kolom di users = roles (integer id role)
    }

    /**
     * Akses nama role (misalnya Admin, Kasir, dll.)
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? 'Unknown';
    }

    /**
     * Cek apakah user punya akses fitur tertentu
     */
    public function hasFeature(string $feature): bool
    {
        // This is more efficient as it checks the loaded collection
        return $this->role && $this->role->features->contains('name', $feature);
    }
}
