<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->enum('scope', ['global', 'project'])->default('project')->after('description');
        });

        // Globale Rollen sofort setzen
        DB::table('roles')
            ->whereIn('name', ['system_admin', 'administrator', 'developer'])
            ->update(['scope' => 'global']);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('scope');
        });
    }
};
