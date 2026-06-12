# School Management System

A Laravel 13 application with strict Role-Based Access Control (RBAC) for schools.

## Requirements

- PHP ^8.3
- Composer
- Node.js & npm
- SQLite (default) or MySQL

## Quick Setup

```bash
# Install dependencies and run setup
composer setup
```

This runs: `composer install`, creates `.env`, generates app key, creates storage symlink, runs migrations, seeds roles/permissions, and builds frontend assets.

### Manual Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
npm install && npm run build
```

## Default Seeder Accounts

After running the seeder, create admin/teacher/parent/student users via the admin panel at `/admin/users`.

## Development

```bash
# Start all services (server, queue, logs, Vite)
composer dev
```

## Testing

```bash
composer test
```

## Roles

- **Admin** — Manages users, classes, subjects, settings. Cannot modify teacher-created academic records.
- **Teacher** — Creates assignments, exams, attendance, and results. Owns their content exclusively.
- **Student** — View-only access to schedule, homework, results, and messages.
- **Parent** — View-only access to child's attendance, homework, results, and fees. Cannot create conversations.
