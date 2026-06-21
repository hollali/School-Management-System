<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Room;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ExamSchedule::with('exam', 'class', 'room');

        if ($user->hasRole('Teacher')) {
            $query->whereHas('exam', fn($q) => $q->where('teacher_id', $user->teacher?->id));
        } elseif ($user->hasRole('Student')) {
            $classIds = $user->student->classes->pluck('id');
            $query->whereIn('class_id', $classIds);
        }

        $schedules = $query->orderBy('exam_date')->orderBy('start_time')->paginate(20);
        $exams = Exam::where('is_published', true)->orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();

        return view('exam-schedules.index', compact('schedules', 'exams', 'classes', 'rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'room_id' => 'nullable|exists:rooms,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
        ]);

        // Check for conflicts
        $conflict = ExamSchedule::where('exam_date', $data['exam_date'])
            ->where('class_id', $data['class_id'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['class_id' => 'This class already has a scheduled exam during this time.'])->withInput();
        }

        if (!empty($data['room_id'])) {
            $roomConflict = ExamSchedule::where('exam_date', $data['exam_date'])
                ->where('room_id', $data['room_id'])
                ->where(function ($q) use ($data) {
                    $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
                })
                ->exists();

            if ($roomConflict) {
                return back()->withErrors(['room_id' => 'This room is already booked for this time.'])->withInput();
            }
        }

        ExamSchedule::create($data);

        return redirect()->route('exam-schedules.index')->with('success', 'Exam scheduled successfully.');
    }

    public function update(Request $request, ExamSchedule $schedule)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'room_id' => 'nullable|exists:rooms,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
        ]);

        $schedule->update($data);
        return redirect()->route('exam-schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy(ExamSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('exam-schedules.index')->with('success', 'Schedule removed successfully.');
    }
}
