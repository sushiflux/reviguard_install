# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Environment

The app runs entirely in Docker. All `artisan`, `composer`, and `npm` commands must be executed inside the containers:

```bash
# PHP / Artisan
docker compose exec php php artisan <command>

# Composer
docker compose exec php composer <command>

# Run tests
docker compose exec php php artisan test

# Run a single test file
docker compose exec php php artisan test tests/Feature/ExampleTest.php

# Code style (Laravel Pint)
docker compose exec php ./vendor/bin/pint

# Clear all caches
docker compose exec php php artisan config:clear && php artisan route:clear && php artisan view:clear

# Migrations & Seeder
docker compose exec php php artisan migrate --force
docker compose exec php php artisan db:seed --force

# Tinker (REPL)
docker compose exec php php artisan tinker
```

## Services & Ports

| Service    | URL / Port               |
|------------|--------------------------|
| App        | http://localhost:8084     |
| phpMyAdmin | http://localhost:8085     |
| MySQL      | localhost:3309            |

DB credentials: user `appuser` / password `apppassword`, database `reviguard`.

## Architecture Overview

**Stack:** Laravel 12 · PHP 8.2-FPM · Nginx · MySQL 8 — all via Docker Compose.

### Authentication
Login uses `username` (not `email`). `Auth::attempt(['username' => ..., 'password' => ...])` works because the Eloquent provider resolves any column key dynamically. The custom `LoginController` (`app/Http/Controllers/Auth/`) also checks `is_active` before allowing login.

Initial System-Admin: `RGAdmin` / `RGAdmin`.

### Role System
Two separate role layers:

1. **Global roles** (`user_roles` pivot) — system-wide roles assigned by an Administrator.
2. **Project roles** (`project_user_roles`) — same role set scoped per project.

Roles (slugs): `viewer`, `editor`, `projektleiter`, `developer`, `projektleiter_admin`, `administrator`, `system_admin`.

Helper methods on `User`: `hasRole(string)`, `hasAnyRole(array)`, `hasProjectRole(int, string)`, `isAdmin()`.

The `admin` middleware (`AdminMiddleware`) gates the entire `/admin/*` prefix — it passes only if `isAdmin()` is true (i.e. `is_system_admin === true` OR has `administrator` role).

### Revision Safety
`revisions` table has **no soft-deletes and no hard-delete route**. Entries are superseded rather than deleted: set `replaced_by_revision_id`, `replaced_by_user_id`, and `replaced_at`. Active revisions are those where `replaced_at IS NULL`.

### Views & Layout
All authenticated pages extend `resources/views/layouts/app.blade.php`, which renders the sidebar. The sidebar Admin section is conditionally shown via `auth()->user()->isAdmin()`. No Vite/Tailwind — all CSS is plain inline `<style>` blocks using CSS custom properties defined in the layout.

Color scheme variables (defined in `layouts/app.blade.php` and `auth/login.blade.php`):
- `--c-primary: #0D1B2A` · `--c-secondary: #1E40AF` · `--c-accent1: #06B6D4` · `--c-accent2: #F59E0B` · `--c-neutral: #F1F5F9`

### Key Files
| Path | Purpose |
|------|---------|
| `bootstrap/app.php` | Middleware aliases registered here (`admin`) |
| `routes/web.php` | All routes; admin group uses `middleware(['auth','admin'])` |
| `app/Models/User.php` | Role helpers, `isAdmin()` |
| `database/seeders/RolesSeeder.php` | Seeds all 7 roles |
| `database/seeders/SystemAdminSeeder.php` | Creates `RGAdmin` user |
| `DEVELOPER_DIARY.txt` | Freeform change log for the developer; entries go here before publishing to `version_changelog` table |

### Storage Permissions
PHP-FPM runs as `www-data`. After any `docker compose up`, if the `storage/` directory is freshly mounted from the host (owned by root), run:
```bash
docker compose exec php chown -R www-data:www-data storage bootstrap/cache
```
This is baked into the Dockerfile but may need reapplying when volumes are recreated.
