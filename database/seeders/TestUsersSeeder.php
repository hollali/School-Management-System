<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentUser = \App\Models\User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'Test Student', 'password' => bcrypt('Password123')]
        );
        if (method_exists($studentUser, 'assignRole')) {
            $studentUser->assignRole('Student');
        }
        \App\Models\Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            ['admission_number' => 'TS1001']
        );

        $parentUser = \App\Models\User::firstOrCreate(
            ['email' => 'parent@example.com'],
            ['name' => 'Test Parent', 'password' => bcrypt('Password123')]
        );
        if (method_exists($parentUser, 'assignRole')) {
            $parentUser->assignRole('Parent');
        }
        \App\Models\ParentProfile::firstOrCreate(
            ['user_id' => $parentUser->id],
            ['relationship' => 'Mother']
        );
    }
}
