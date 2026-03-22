<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'content',
        'type',
        'version',
    ];

    protected function casts(): array
    {
        return ['replaced_at' => 'datetime'];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replacedByRevision()
    {
        return $this->belongsTo(Revision::class, 'replaced_by_revision_id');
    }

    public function replacedByUser()
    {
        return $this->belongsTo(User::class, 'replaced_by_user_id');
    }

    public function isReplaced(): bool
    {
        return $this->replaced_at !== null;
    }
}
