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
            $table->renameColumn('name', 'vorname');
            $table->string('nachname', 100)->nullable()->after('vorname');
        });

        // Bestehende Namen aufteilen: erstes Wort → vorname, Rest → nachname
        DB::table('users')->get()->each(function ($user) {
            $parts = explode(' ', trim($user->vorname), 2);
            DB::table('users')->where('id', $user->id)->update([
                'vorname'  => $parts[0],
                'nachname' => $parts[1] ?? null,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nachname');
            $table->renameColumn('vorname', 'name');
        });
    }
};
