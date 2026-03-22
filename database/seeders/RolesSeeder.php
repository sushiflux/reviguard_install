<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => 'viewer',
                'display_name' => 'Viewer',
                'description'  => 'Kann nur berechtigte Projekte lesen.',
            ],
            [
                'name'         => 'editor',
                'display_name' => 'Editor',
                'description'  => 'Kann eigene Revisionen hinzufügen und ersetzen.',
            ],
            [
                'name'         => 'projektleiter',
                'display_name' => 'Projektleiter',
                'description'  => 'Kann alle Revisionen im Projekt ersetzen.',
            ],
            [
                'name'         => 'developer',
                'display_name' => 'Developer',
                'description'  => 'Zuständig für das System, kann das Versions-Changelog pflegen.',
            ],
            [
                'name'         => 'projektleiter_admin',
                'display_name' => 'Projektleiter-Admin',
                'description'  => 'Hat Zugriff auf alle Projekte.',
            ],
            [
                'name'         => 'administrator',
                'display_name' => 'Administrator',
                'description'  => 'Kann User anlegen, Rollen vergeben und Einstellungen verwalten.',
            ],
            [
                'name'         => 'system_admin',
                'display_name' => 'System-Admin',
                'description'  => 'Initialer Systemadministrator. Kann nicht gelöscht werden.',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
