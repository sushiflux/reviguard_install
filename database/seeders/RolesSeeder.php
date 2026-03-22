<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // ── Globale Rollen (kein Projekt) ──────────────────────
            [
                'name'         => 'system_admin',
                'display_name' => 'System-Admin',
                'description'  => 'Initialer Systemadministrator. Kann nicht gelöscht werden.',
                'scope'        => 'global',
            ],
            [
                'name'         => 'administrator',
                'display_name' => 'Administrator',
                'description'  => 'Kann User anlegen, Rollen vergeben und Einstellungen verwalten.',
                'scope'        => 'global',
            ],
            [
                'name'         => 'developer',
                'display_name' => 'Developer',
                'description'  => 'Zuständig für das System, pflegt das Versions-Changelog.',
                'scope'        => 'global',
            ],

            // ── Projektgebundene Rollen ────────────────────────────
            [
                'name'         => 'projektleiter_admin',
                'display_name' => 'Projektleiter-Admin',
                'description'  => 'Hat Zugriff auf alle Projekte.',
                'scope'        => 'project',
            ],
            [
                'name'         => 'projektleiter',
                'display_name' => 'Projektleiter',
                'description'  => 'Kann alle Revisionen im Projekt ersetzen.',
                'scope'        => 'project',
            ],
            [
                'name'         => 'editor',
                'display_name' => 'Editor',
                'description'  => 'Kann eigene Revisionen hinzufügen und ersetzen.',
                'scope'        => 'project',
            ],
            [
                'name'         => 'viewer',
                'display_name' => 'Viewer',
                'description'  => 'Kann nur berechtigte Projekte lesen.',
                'scope'        => 'project',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
