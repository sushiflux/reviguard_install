<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'vorname',
        'nachname',
        'username',
        'email',
        'password',
        'is_active',
        'is_system_admin',
        'dashboard_view',
        'dashboard_sort',
    ];

    public function getNameAttribute(): string
    {
        return trim($this->vorname . ' ' . $this->nachname);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_system_admin'   => 'boolean',
        ];
    }

    // ----------------------------------------------------------------
    //  Relationships
    // ----------------------------------------------------------------

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function projectRoles()
    {
        return $this->hasMany(ProjectUserRole::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'created_by');
    }

    // ----------------------------------------------------------------
    //  Helpers
    // ----------------------------------------------------------------

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasProjectRole(int $projectId, string $role): bool
    {
        return $this->projectRoles()
            ->where('project_id', $projectId)
            ->whereHas('role', fn($q) => $q->where('name', $role))
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->is_system_admin || $this->hasRole('administrator');
    }

    public function canCreateProjects(): bool
    {
        return $this->is_system_admin || $this->hasRole('projektleiter_admin');
    }

    public function canSeeAllProjects(): bool
    {
        return $this->is_system_admin
            || $this->hasAnyRole(['administrator', 'projektleiter_admin', 'developer']);
    }

    public function canEditProject(int $projectId): bool
    {
        if ($this->is_system_admin) return true;
        if ($this->hasRole('administrator')) return true;
        return $this->projectRoles()
            ->where('project_id', $projectId)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['editor', 'projektleiter']))
            ->exists();
    }
}
