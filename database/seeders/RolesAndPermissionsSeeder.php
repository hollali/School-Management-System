<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher']);
        $parent = Role::firstOrCreate(['name' => 'Parent']);
        $student = Role::firstOrCreate(['name' => 'Student']);

        $permissions = [
            // User management (Admin only)
            'create-users', 'view-users', 'edit-users', 'delete-users',
            // Class/Subject management (Admin)
            'create-classes', 'edit-classes', 'delete-classes',
            'create-subjects', 'edit-subjects', 'delete-subjects',
            // Teacher permissions
            'create-assignments', 'edit-own-assignments', 'delete-own-assignments',
            'create-exams', 'edit-own-exams', 'delete-own-exams',
            'record-attendance', 'edit-own-attendance', 'delete-own-attendance',
            'create-results', 'edit-own-results', 'delete-own-results',
            'grade-submissions',
            // View permissions
            'view-students', 'view-assignments', 'view-exams',
            'view-results', 'view-attendance', 'view-fees',
            'view-classes', 'view-subjects',
            // Messaging
            'send-messages', 'view-messages',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin->givePermissionTo(Permission::all());

        $teacher->givePermissionTo([
            'view-students', 'view-assignments', 'view-exams',
            'view-results', 'view-attendance', 'view-fees',
            'view-classes', 'view-subjects',
            'send-messages', 'view-messages',
            'create-assignments', 'edit-own-assignments', 'delete-own-assignments',
            'create-exams', 'edit-own-exams', 'delete-own-exams',
            'record-attendance', 'edit-own-attendance', 'delete-own-attendance',
            'create-results', 'edit-own-results', 'delete-own-results',
            'grade-submissions',
        ]);

        $student->givePermissionTo([
            'view-assignments', 'view-exams', 'view-results',
            'view-attendance', 'view-classes', 'view-subjects',
            'view-messages', 'send-messages',
        ]);

        $parent->givePermissionTo([
            'view-assignments', 'view-results', 'view-attendance',
            'view-fees', 'view-messages',
        ]);
    }
}
