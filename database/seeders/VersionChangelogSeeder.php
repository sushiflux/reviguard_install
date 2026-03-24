<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VersionChangelog;
use Illuminate\Database\Seeder;

class VersionChangelogSeeder extends Seeder
{
    public function run(): void
    {
        // Nur eintragen wenn die Tabelle noch leer ist
        if (VersionChangelog::count() > 0) {
            return;
        }

        $admin = User::where('username', 'RGAdmin')->first();

        VersionChangelog::create([
            'version'    => config('app.version', '0.5.1'),
            'title'      => 'Initiale Installation',
            'content'    => 'ReviGuard wurde erfolgreich installiert und ist einsatzbereit.',
            'created_by' => $admin?->id,
            'released_at' => now(),
        ]);
    }
}
