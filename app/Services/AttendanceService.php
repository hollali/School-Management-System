<?php

namespace App\Services;

use App\Events\AttendanceCorrected;
use App\Events\AttendanceThresholdReached;
use App\Events\StudentMarkedAbsent;
use App\Events\StudentMarkedLate;
use App\Helpers\ActivityLogger;
use App\Models\Attendance;
use App\Models\AttendanceAuditLog;
use App\Models\AttendanceRecord;
use App\Models\Holiday;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public const STATUSES = ['present', 'absent', 'late', 'excused'];

    public const THRESHOLD_DEFAULT = 75.0;

    public static function isWeekend(string $date): bool
    {
        $day = Carbon::parse($date)->dayOfWeek;
        return $day === Carbon::SATURDAY || $day === Carbon::SUNDAY;
    }

    public static function isHoliday(string $date): bool
    {
        return Holiday::where('holiday_date', $date)->exists();
    }

    public static function isSchoolDay(string $date): bool
    {
        return !static::isWeekend($date) && !static::isHoliday($date);
    }

    public static function getSchoolDaysInRange(string $startDate, string $endDate): Collection
    {
        $holidayDates = Holiday::pluck('holiday_date')->map(fn($d) => $d->format('Y-m-d'))->toArray();
        $days = collect();

        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $isWeekend = $current->dayOfWeek === Carbon::SATURDAY || $current->dayOfWeek === Carbon::SUNDAY;
            if (!$isWeekend && !in_array($dateStr, $holidayDates)) {
                $days->push($dateStr);
            }
            $current->addDay();
        }

        return $days;
    }

    public function getOrCreateAttendance(int $classId, string $date, ?int $subjectId = null, ?int $teacherId = null): Attendance
    {
        $dateParsed = Carbon::parse($date);

        $query = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $dateParsed)
            ->first();

        if (!$query) {
            $query = Attendance::create([
                'class_id' => $classId,
                'attendance_date' => $dateParsed,
                'subject_id' => $subjectId,
                'created_by' => Auth::id(),
                'teacher_id' => $teacherId ?? Auth::user()?->teacher?->id,
                'notes' => null,
            ]);

            $schoolClass = SchoolClass::find($classId);
            if ($schoolClass) {
                foreach ($schoolClass->students as $student) {
                    AttendanceRecord::create([
                        'attendance_id' => $query->id,
                        'student_id' => $student->id,
                        'status' => 'present',
                        'remarks' => null,
                    ]);
                }
            }

            ActivityLogger::log('attendance-created', 'Attendance', $query->id,
                'Created attendance for class #' . $classId . ' on ' . $date);
        } elseif ($subjectId !== null && $query->subject_id !== $subjectId) {
            $query->update(['subject_id' => $subjectId]);
        }

        return $query;
    }

    public function markStudentAttendance(AttendanceRecord $record, string $status, ?string $remarks = null): AttendanceRecord
    {
        $oldStatus = $record->status;

        if (!in_array($status, self::STATUSES)) {
            throw new \InvalidArgumentException("Invalid status: $status");
        }

        $record->update([
            'status' => $status,
            'remarks' => $remarks,
        ]);

        $this->logAudit($record, 'updated', [
            'status' => $oldStatus,
            'remarks' => $record->getOriginal('remarks'),
        ], [
            'status' => $status,
            'remarks' => $remarks,
        ]);

        if ($oldStatus !== $status) {
            if ($status === 'absent') {
                event(new StudentMarkedAbsent($record));
            } elseif ($status === 'late') {
                event(new StudentMarkedLate($record));
            }

            event(new AttendanceCorrected($record, $oldStatus, $status));

            $this->checkThreshold($record->student);
        }

        return $record->fresh();
    }

    public function markBulkAttendance(int $attendanceId, array $studentStatuses): void
    {
        DB::transaction(function () use ($attendanceId, $studentStatuses) {
            foreach ($studentStatuses as $studentId => $data) {
                $record = AttendanceRecord::where('attendance_id', $attendanceId)
                    ->where('student_id', $studentId)
                    ->first();

                if ($record) {
                    $this->markStudentAttendance(
                        $record,
                        $data['status'] ?? 'present',
                        $data['remarks'] ?? null
                    );
                }
            }
        });
    }

    public function getStudentAttendanceSummary(Student $student, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = AttendanceRecord::where('student_id', $student->id);

        if ($startDate) {
            $query->whereHas('attendance', fn($q) => $q->where('attendance_date', '>=', $startDate));
        }
        if ($endDate) {
            $query->whereHas('attendance', fn($q) => $q->where('attendance_date', '<=', $endDate));
        }

        $this->applySchoolDayFilter($query);

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $excused = (clone $query)->where('status', 'excused')->count();

        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentage' => $percentage,
        ];
    }

    public function getClassAttendanceSummary(int $classId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $holidayDates = Holiday::pluck('holiday_date')->map(fn($d) => $d->format('Y-m-d'))->toArray();

        $attendanceIds = Attendance::forClass($classId)
            ->when($startDate, fn($q) => $q->where('attendance_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('attendance_date', '<=', $endDate))
            ->whereRaw("strftime('%w', attendance_date) NOT IN ('0', '6')")
            ->when(!empty($holidayDates), fn($q) => $q->whereNotIn('attendance_date', $holidayDates))
            ->pluck('id');

        $students = SchoolClass::find($classId)?->students ?? collect();

        return $students->map(function ($student) use ($attendanceIds) {
            $records = AttendanceRecord::whereIn('attendance_id', $attendanceIds)
                ->where('student_id', $student->id)
                ->get();

            $total = $records->count();
            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();
            $excused = $records->where('status', 'excused')->count();

            return [
                'student' => $student,
                'total' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        });
    }

    public function getLowAttendanceStudents(float $threshold = self::THRESHOLD_DEFAULT): Collection
    {
        $students = Student::with('user', 'classes')->get();

        return $students->filter(function ($student) use ($threshold) {
            $summary = $this->getStudentAttendanceSummary($student);
            return $summary['total'] > 0 && $summary['percentage'] < $threshold;
        })->values();
    }

    public function checkThreshold(Student $student, float $threshold = self::THRESHOLD_DEFAULT): void
    {
        $summary = $this->getStudentAttendanceSummary($student);

        if ($summary['total'] > 0 && $summary['percentage'] < $threshold) {
            event(new AttendanceThresholdReached($student, $summary['percentage'], $threshold));
        }
    }

    public function getOverallSchoolSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = AttendanceRecord::query();

        if ($startDate) {
            $query->whereHas('attendance', fn($q) => $q->where('attendance_date', '>=', $startDate));
        }
        if ($endDate) {
            $query->whereHas('attendance', fn($q) => $q->where('attendance_date', '<=', $endDate));
        }

        $this->applySchoolDayFilter($query);

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $excused = (clone $query)->where('status', 'excused')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    private function applySchoolDayFilter($query): void
    {
        $holidayDates = Holiday::pluck('holiday_date')->map(fn($d) => $d->format('Y-m-d'))->toArray();

        $query->whereHas('attendance', function ($q) use ($holidayDates) {
            $q->whereRaw("strftime('%w', attendance_date) NOT IN ('0', '6')");
            if (!empty($holidayDates)) {
                $q->whereNotIn('attendance_date', $holidayDates);
            }
        });
    }

    private function logAudit(AttendanceRecord $record, string $action, array $oldValues, array $newValues): void
    {
        AttendanceAuditLog::create([
            'attendance_type' => 'student',
            'attendance_id' => $record->attendance_id,
            'record_id' => $record->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
