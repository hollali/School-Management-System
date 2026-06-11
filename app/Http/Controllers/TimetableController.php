<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Timetable;
use App\Models\Subject;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index()
    {
        $timetables = Timetable::with(['schoolClass', 'subject'])->latest()->paginate(15);

        return view('timetables.index', compact('timetables'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('timetables.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        Timetable::create($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry saved successfully.');
    }

    public function edit(Timetable $timetable)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('timetables.edit', compact('timetable', 'classes', 'subjects'));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'day' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $timetable->update($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->route('timetables.index')->with('success', 'Timetable entry deleted successfully.');
    }
}
