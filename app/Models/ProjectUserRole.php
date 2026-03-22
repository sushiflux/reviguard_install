<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUserRole extends Model
{
    protected $fillable = ['user_id', 'project_id', 'role_id', 'assigned_by'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
