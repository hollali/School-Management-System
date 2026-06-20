<?php

namespace App\Events;

use App\Models\AttendanceRecord;
use Illuminate\Foundation\Events\Dispatchable;

class AttendanceCorrected
{
    use Dispatchable;

    public AttendanceRecord $record;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(AttendanceRecord $record, string $oldStatus, string $newStatus)
    {
        $this->record = $record;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
