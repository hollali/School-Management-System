<?php

namespace Database\Seeders;

use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample classes first
        $classes = SchoolClass::factory(5)->create();

        // Create parent profiles
        $parents = ParentProfile::factory(10)->create();

        // Create 50 students
        Student::factory(50)
            ->create()
            ->each(function ($student) use ($classes, $parents) {
                // Assign random parent (80% chance)
                if (rand(1, 100) <= 80) {
                    $student->update(['parent_id' => $parents->random()->id]);
                }

                // Assign to a class
                $student->classes()->attach(
                    $classes->random()->id,
                    [
                        'assigned_at' => now(),
                        'status' => 'active',
                    ]
                );
            });

        $this->command->info('Student seeder completed: 50 students created');
    }
}
