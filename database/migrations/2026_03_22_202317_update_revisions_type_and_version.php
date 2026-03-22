<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change type from ENUM to TEXT to support multiple types (comma-separated)
        DB::statement('ALTER TABLE revisions MODIFY type TEXT NULL');
        // Change content to LONGTEXT for JSON storage
        DB::statement('ALTER TABLE revisions MODIFY content LONGTEXT NOT NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE revisions MODIFY type ENUM('update','fix','change','release','hotfix') NOT NULL");
        DB::statement('ALTER TABLE revisions MODIFY content TEXT NOT NULL');
    }
};
