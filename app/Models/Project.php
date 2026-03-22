<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function userRoles()
    {
        return $this->hasMany(ProjectUserRole::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    // Nur aktive (nicht ersetzte) Revisionen
    public function activeRevisions()
    {
        return $this->hasMany(Revision::class)->whereNull('replaced_at');
    }
}
