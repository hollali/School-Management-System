<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceThresholdReached
{
    use Dispatchable;

    public Student $student;
    public float $percentage;
    public float $threshold;

    public function __construct(Student $student, float $percentage, float $threshold)
    {
        $this->student = $student;
        $this->percentage = $percentage;
        $this->threshold = $threshold;
    }
}
