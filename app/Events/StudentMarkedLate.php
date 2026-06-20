<?php

namespace App\Events;

use App\Models\AttendanceRecord;
use Illuminate\Foundation\Events\Dispatchable;

class StudentMarkedLate
{
    use Dispatchable;

    public AttendanceRecord $record;

    public function __construct(AttendanceRecord $record)
    {
        $this->record = $record;
    }
}
