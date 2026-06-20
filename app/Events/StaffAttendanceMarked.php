<?php

namespace App\Events;

use App\Models\StaffAttendance;
use Illuminate\Foundation\Events\Dispatchable;

class StaffAttendanceMarked
{
    use Dispatchable;

    public StaffAttendance $staffAttendance;

    public function __construct(StaffAttendance $staffAttendance)
    {
        $this->staffAttendance = $staffAttendance;
    }
}
