<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Erst eigenen Index auf user_id anlegen, damit MySQL den Unique-Index loslässt
        DB::statement('ALTER TABLE project_user_roles ADD KEY pur_user_id_idx (user_id)');
        DB::statement('ALTER TABLE project_user_roles DROP INDEX project_user_roles_user_id_project_id_role_id_unique');
        // Neuer Unique: ein User, ein Projekt → eine Rolle
        DB::statement('ALTER TABLE project_user_roles ADD UNIQUE KEY pur_user_project_unique (user_id, project_id)');
        // Temporären Index wieder entfernen (der neue Unique deckt user_id ab)
        DB::statement('ALTER TABLE project_user_roles DROP KEY pur_user_id_idx');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE project_user_roles ADD KEY pur_user_id_idx (user_id)');
        DB::statement('ALTER TABLE project_user_roles DROP INDEX pur_user_project_unique');
        DB::statement('ALTER TABLE project_user_roles ADD UNIQUE KEY project_user_roles_user_id_project_id_role_id_unique (user_id, project_id, role_id)');
        DB::statement('ALTER TABLE project_user_roles DROP KEY pur_user_id_idx');
    }
};
