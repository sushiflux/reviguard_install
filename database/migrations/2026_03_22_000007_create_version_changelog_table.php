<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('version_changelog', function (Blueprint $table) {
            $table->id();
            $table->string('version');                         // z.B. 1.0.0
            $table->string('title');
            $table->text('content');                          // Markdown-fähig
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('version_changelog');
    }
};
