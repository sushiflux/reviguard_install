<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Weist einem User eine Rolle innerhalb eines bestimmten Projekts zu
        Schema::create('project_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();

            $table->unique(['user_id', 'project_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user_roles');
    }
};
