# School Management System

A Laravel 13 application for managing schools with strict Role-Based Access Control (RBAC). Features student/teacher/parent management, attendance tracking, assignments, exams, results, fee management, real-time messaging, notifications, and activity auditing.

## Roles

| Role | Permissions |
|------|------------|
| **Admin** | Full access to users, classes, subjects, fees, payments, receipts. Cannot modify teacher-created academic records (assignments, exams, attendance, results). |
| **Teacher** | Creates and owns assignments, exams, attendance records, and results. Can grade submissions. |
| **Student** | View-only access to schedule, assignments, results, attendance, and messages. Can message other students. |
| **Parent** | View-only access to linked child's attendance, results, fees, and messages. Cannot create conversations. |

## Messaging Rules

| Role | Can message |
|------|------------|
| **Student** | Other students only (private & group chats). Class-based groups are student-only. |
| **Teacher** | Teachers & Administrators only. Department/staff group chats. |
| **Admin** | Teachers & Administrators only. Administrative group communications. |
| **Parent** | No messaging privileges. |

## Features

- **Role-based dashboards** — each role sees relevant stats and quick actions
- **User management** — admins create/manage all accounts (no self-registration)
- **Student management** — profiles, class assignments, parent linking, bulk CSV import
- **Class & subject management** — assign teachers, set capacity, individual & bulk student assignment
- **Attendance tracking** — daily records per class with per-student status (present/absent/late/excused). Weekends and holidays auto-excluded from summaries. Staff attendance with self check-in/check-out. Role-based dashboards with low-attendance alerts. 5 report types (student, class, subject, teacher, date range). Audit trail for all changes.
- **Holiday management** — Admins define public, school, and exam holidays. Recurring support. Attendance summaries skip holiday/weekend dates automatically. Visual warnings when marking attendance on non-school days.
- **Assignments & submissions** — teachers create, students submit, teachers grade with feedback
- **Exams & results** — exam scheduling, score entry with grade calculation
- **Fee management** — multi-level fee structures (categories → structures → items), auto invoice generation, student discounts (percentage/fixed, scoped per-student/class/global), payment processing with status tracking (completed/failed/refunded), receipt generation, overdue detection and notifications, finance dashboard, and 5 report types
- **Real-time messaging** — conversations with participants, group support, reactions, file attachments, reply/forward/edit/delete, typing indicators, Echo broadcasting
- **Notifications** — centralized notification list with broadcast events
- **Activity logging** — all critical actions logged for audit trail via `ActivityLogger`
- **Profile photos** — upload or auto-generated avatars
- **Dark mode** — manual toggle with sun/moon button in sidebar; preference persisted to localStorage
- **Responsive layout** — mobile-friendly with off-canvas navigation
- **Collapsible sidebar** — persists across page loads

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

Starts the PHP server, queue worker, log watcher, and Vite dev server concurrently. Real-time messaging requires the queue worker for Echo broadcasting via Reverb.

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
| `app/Http/Controllers/` | Resource controllers for all entities |
| `app/Http/Controllers/Admin/` | Admin-only user management |
| `app/Http/Requests/` | Form request validation |
| `app/Policies/` | Authorization policies for roles and ownership |
| `app/Events/` | Broadcast events and domain events (messaging, attendance, fees, submissions) |
| `app/Listeners/` | Event listeners for notifications and broadcasting |
| `app/Helpers/` | `ActivityLogger` for audit trails |
| `app/Services/` | Business logic services: `AttendanceService`, `FeeService` |
| `app/Models/` | Eloquent models: User, Student, Teacher, Attendance, Fee, Payment, Receipt, Holiday, Conversation, Message, etc. |
| `database/migrations/` | Schema migrations for all features |
| `database/seeders/` | `RolesAndPermissionsSeeder` with granular permissions |
| `resources/views/` | Role-appropriate Blade views with collapsible sidebar layout |
| `resources/views/attendance/` | Attendance marking, dashboards, reports, student history |
| `resources/views/staff-attendance/` | Staff attendance CRUD + self check-in/out |
| `resources/views/holidays/` | Holiday management (Admin CRUD) |
| `resources/views/fees/` | Fee invoice listing and detail views |
| `resources/views/payments/` | Payment history, processing, parent portal |
| `resources/views/receipts/` | Receipt generation and listing |
| `resources/views/fee-structures/` | Fee structure management |
| `resources/views/fee-categories/` | Fee category management |
| `resources/views/discounts/` | Student discount administration |
| `resources/views/finance/` | Finance dashboard and reports |
| `resources/views/conversations/` | Real-time messaging UI with Alpine.js and Echo |
| `config/messaging.php` | Messaging configuration (allowed MIME types, etc.) |
| `routes/web.php` | All application routes organized by feature |
| `routes/channels.php` | Echo broadcasting channels for messaging |
| `routes/auth.php` | Authentication routes (registration removed) |

## Tech Stack

- **Framework**: Laravel 13
- **Authorization**: Spatie Laravel Permission + Laravel Policies
- **Broadcasting**: Laravel Reverb (WebSocket server) + Echo
- **Frontend**: Alpine.js, Tailwind CSS (with dark mode class strategy), Font Awesome 6
- **Icons**: Font Awesome (free-solid and free-regular)
- **Database**: SQLite / MySQL
- **Assets**: Vite
