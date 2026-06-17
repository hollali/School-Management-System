# School Management System

A Laravel 13 application for managing schools with strict Role-Based Access Control (RBAC). Features student/teacher/parent management, attendance tracking, assignments, exams, results, fee management, messaging, and activity auditing.

## Roles

| Role | Permissions |
|------|------------|
| **Admin** | Full access to users, classes, subjects, fees, payments, receipts. Cannot modify teacher-created academic records (assignments, exams, attendance, results). |
| **Teacher** | Creates and owns assignments, exams, attendance records, and results. Can grade submissions. |
| **Student** | View-only access to schedule, assignments, results, attendance, and messages. |
| **Parent** | View-only access to linked child's attendance, results, fees, and messages. Cannot create conversations. |

## Features

- **Role-based dashboards** — each role sees relevant stats and quick actions
- **User management** — admins create/manage all accounts (no self-registration)
- **Student management** — profiles, class assignments, parent linking
- **Class & subject management** — assign teachers, set capacity
- **Attendance** — daily records per class with per-student status (present/absent/late/excused)
- **Assignments & submissions** — teachers create, students submit, teachers grade with feedback
- **Exams & results** — exam scheduling, score entry with grade calculation
- **Fee management** — invoices, payments, receipts with status tracking
- **Messaging** — conversations with participants, group support
- **Notifications** — centralized notification list
- **Profile photos** — upload or auto-generated avatars
- **Activity logging** — all critical actions logged for audit
- **Collapsible sidebar** — persists across page loads
- **Dark mode** — manual toggle with sun/moon button in sidebar; preference persisted to localStorage
- **Responsive layout** — mobile-friendly with off-canvas navigation

## Requirements

- PHP ^8.3
- Composer
- Node.js & npm
- SQLite (default) or MySQL

## Quick Setup

```bash
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

## Creating Users

After setup, visit `/admin/users` as an Admin to create accounts for teachers, students, and parents. There is no self-registration — all accounts are created by administrators.

## Development

```bash
composer dev
```

Starts the PHP server, queue worker, log watcher, and Vite dev server concurrently.

## Testing

```bash
composer test
```

## Project Structure

| Directory | Purpose |
|-----------|---------|
| `app/Http/Controllers/` | Resource controllers for all entities |
| `app/Http/Controllers/Admin/` | Admin-only user management |
| `app/Http/Requests/` | Form request validation |
| `app/Policies/` | Authorization policies for roles and ownership |
| `app/Helpers/` | `ActivityLogger` for audit trails |
| `database/migrations/` | Schema migrations including `activity_logs`, `conversation_user` pivot |
| `database/seeders/` | `RolesAndPermissionsSeeder` with granular permissions |
| `resources/views/` | Role-appropriate Blade views with collapsible sidebar layout |
| `routes/web.php` | All application routes organized by feature |
| `routes/auth.php` | Authentication routes (registration removed) |

## Tech Stack

- **Framework**: Laravel 13
- **Authorization**: Spatie Laravel Permission + Laravel Policies
- **Frontend**: Alpine.js, Tailwind CSS (with dark mode class strategy), Font Awesome 6
- **Icons**: Font Awesome (free-solid)
- **Database**: SQLite / MySQL
- **Assets**: Vite
