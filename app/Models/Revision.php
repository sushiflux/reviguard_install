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
        'replaced_by_revision_id',
        'replaced_by_user_id',
        'replaced_at',
    ];

    protected function casts(): array
    {
        return ['replaced_at' => 'datetime'];
    }

    // ----------------------------------------------------------------
    //  Relationships
    // ----------------------------------------------------------------

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

    /**
     * The revision that this revision replaced (i.e. the old/superseded one).
     */
    public function predecessor()
    {
        return $this->hasOne(Revision::class, 'replaced_by_revision_id');
    }

    public function replacedByUser()
    {
        return $this->belongsTo(User::class, 'replaced_by_user_id');
    }

    // ----------------------------------------------------------------
    //  Helpers
    // ----------------------------------------------------------------

    public function isReplaced(): bool
    {
        return $this->replaced_at !== null;
    }

    /**
     * Returns the structured entries: [['type' => '...', 'content' => '...'], ...]
     */
    public function getEntriesAttribute(): array
    {
        $decoded = json_decode($this->content, true);
        if (is_array($decoded) && !empty($decoded) && isset($decoded[0]['type'])) {
            return $decoded;
        }
        // Legacy plain-text fallback
        return [['type' => 'update', 'content' => $this->content]];
    }

    /**
     * Returns unique types used in this revision's entries.
     */
    public function getTypesListAttribute(): array
    {
        return array_values(array_unique(array_column($this->entries, 'type')));
    }

    /**
     * Auto-generate next version for a project (1.0, 1.1, 1.2, ...)
     */
    public static function nextVersion(int $projectId): string
    {
        $count = self::where('project_id', $projectId)->count();
        return '1.' . $count;
    }
}
