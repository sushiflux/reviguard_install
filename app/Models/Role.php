<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'scope'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function projectUserRoles()
    {
        return $this->hasMany(ProjectUserRole::class);
    }

    public function isGlobal(): bool
    {
        return $this->scope === 'global';
    }

    public function isProjectScoped(): bool
    {
        return $this->scope === 'project';
    }
}
