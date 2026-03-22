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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dashboard_view', 10)->default('tile')->after('is_system_admin');
            $table->string('dashboard_sort', 4)->default('az')->after('dashboard_view');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dashboard_view', 'dashboard_sort']);
        });
    }
};
