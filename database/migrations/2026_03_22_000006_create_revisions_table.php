<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Revisionssicher: KEIN softDelete, Einträge dürfen nur ersetzt werden
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['update', 'fix', 'change', 'release', 'hotfix']);
            $table->string('version')->nullable();

            // Ersatz-Mechanismus (statt Löschen)
            $table->foreignId('replaced_by_revision_id')->nullable()->constrained('revisions')->nullOnDelete();
            $table->foreignId('replaced_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replaced_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
