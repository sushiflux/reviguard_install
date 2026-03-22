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

### Authentication & 2FA
Login uses `username` (not `email`). The custom `LoginController` checks `is_active` before allowing login, then checks if 2FA is required.

**2FA flow:** After a successful credential check, if `$user->requiresTwoFactor()` is true, the user is immediately logged out, their ID is stored in `session('2fa_pending_user_id')`, and they are redirected to `/2fa`. The `2fa.pending` middleware (`RequirePendingTwoFactor`) guards all `/2fa/*` routes. `Auth::loginUsingId()` is only called after successful TOTP or WebAuthn verification.

**Supported methods:** TOTP via `pragmarx/google2fa` + `bacon/bacon-qr-code`; WebAuthn (YubiKey) via `laragear/webauthn` v4.1. The `User` model implements `WebAuthnAuthenticatable` and uses the `WebAuthnAuthentication` trait.

**Admin global policy:** stored as `system_settings` key `2fa_policy` (values: `none` | `any` | `totp` | `webauthn`). `User::requiresTwoFactor()` returns true if the user has any 2FA enabled, OR if the global policy is not `none`.

Initial System-Admin: `RGAdmin` / `RGAdmin`.

### Role System
Two separate role layers:

1. **Global roles** (`user_roles` pivot) — system-wide roles assigned by an Administrator.
2. **Project roles** (`project_user_roles`) — same role set scoped per project.

Roles (slugs): `viewer`, `editor`, `projektleiter`, `developer`, `projektleiter_admin`, `administrator`, `system_admin`.

Helper methods on `User`: `hasRole(string)`, `hasAnyRole(array)`, `hasProjectRole(int, string)`, `isAdmin()`.

The `admin` middleware gates the entire `/admin/*` prefix — passes only if `isAdmin()` is true (`is_system_admin === true` OR has `administrator` role).

### Revision Safety
`revisions` table has **no soft-deletes and no hard-delete route**. Entries are superseded rather than deleted: set `replaced_by_revision_id`, `replaced_by_user_id`, and `replaced_at`. Active revisions are those where `replaced_at IS NULL`.

**Predecessor chain display:** `ProjectController::show()` builds a `$replacedMap` — all replaced revisions keyed by `replaced_by_revision_id` — loaded in a single query. Each active revision walks this map in PHP to build its full predecessor chain without recursive eager loading.

### Views & Layout
All authenticated pages extend `resources/views/layouts/app.blade.php`. No Vite/Tailwind — all CSS is plain inline `<style>` blocks using CSS custom properties.

Color scheme variables (defined in `layouts/app.blade.php` and `auth/login.blade.php`):
- `--c-primary: #0D1B2A` · `--c-secondary: #1E40AF` · `--c-accent1: #06B6D4` · `--c-accent2: #F59E0B` · `--c-neutral: #F1F5F9`

The `2fa/challenge.blade.php` is a standalone page (does **not** extend `layouts/app`) — it mirrors the login page style.

**Dynamic timeline (projects/show.blade.php):** Do NOT use Blade `$loop->first/$loop->last` for visual styling in the journal view. JS owns all timeline visual state and recalculates after every filter operation (`updateJournalTimeline()`). Predecessor `<tr>` rows must use `display:table-row` (not `display:block`) to preserve colspan behavior.

### Tab-based Admin & Profile Pages
Several pages use a URL-param tab system (`?tab=<name>`). JS reads `request('tab', '<default>')` in Blade and `history.replaceState` keeps the tab on form submits. Old dedicated routes redirect to the combined page:

| Page | Route | Tabs |
|------|-------|------|
| `admin/access` | `admin.access` | `benutzer`, `matrix` |
| `admin/settings` | `admin.settings` | `system`, `sicherheit`, `system-admins` |
| `profile/settings` | `profile.settings` | `darstellung`, `passwort`, `2fa` |

The WebAuthn scope name is `whereEnabled()` — **not** `enabled()`. Use `$user->webAuthnCredentials()->whereEnabled()->...` everywhere.

### Key Files
| Path | Purpose |
|------|---------|
| `bootstrap/app.php` | Middleware aliases: `admin`, `2fa.pending` |
| `routes/web.php` | All routes; admin group uses `middleware(['auth','admin'])` |
| `app/Models/User.php` | Role helpers, `isAdmin()`, 2FA helpers |
| `app/Models/SystemSetting.php` | Key-value settings table; `SystemSetting::get($key, $default)` / `::set($key, $value)` |
| `resources/views/admin/access.blade.php` | Benutzer & Berechtigungen (2 tabs) |
| `resources/views/admin/settings.blade.php` | Admin-Einstellungen (3 tabs) |
| `resources/views/profile/settings.blade.php` | Profil-Einstellungen (3 tabs) |
| `database/seeders/RolesSeeder.php` | Seeds all 7 roles |
| `database/seeders/SystemAdminSeeder.php` | Creates `RGAdmin` user |
| `DEVELOPER_DIARY.txt` | Freeform change log for the developer; entries go here before publishing to `version_changelog` table |

### Storage Permissions
PHP-FPM runs as `www-data`. After any `docker compose up`, if the `storage/` directory is freshly mounted from the host (owned by root), run:
```bash
docker compose exec php chown -R www-data:www-data storage bootstrap/cache
```
This is baked into the Dockerfile but may need reapplying when volumes are recreated.
