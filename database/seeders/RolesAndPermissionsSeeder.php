<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Teacher']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Parent']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Student']);

        // Optionally create default permissions here and assign to roles
    }
}
