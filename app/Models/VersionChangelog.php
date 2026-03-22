<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionChangelog extends Model
{
    protected $table = 'version_changelog';

    protected $fillable = ['version', 'title', 'content', 'created_by', 'released_at'];

    protected function casts(): array
    {
        return ['released_at' => 'datetime'];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
