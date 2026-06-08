<?php

namespace Database\Factories;

use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admission_number' => 'ADM-' . $this->faker->unique()->numerify('######'),
            'date_of_birth' => $this->faker->dateTimeBetween('-18 years', '-5 years'),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'parent_id' => null,
        ];
    }
}
