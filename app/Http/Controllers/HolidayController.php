<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Holiday::class);
        $holidays = Holiday::orderBy('holiday_date', 'desc')->paginate(20);
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        $this->authorize('create', Holiday::class);
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Holiday::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date',
            'type' => 'required|in:public,school,exam',
            'recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday created successfully.');
    }

    public function edit(Holiday $holiday)
    {
        $this->authorize('update', $holiday);
        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $this->authorize('update', $holiday);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,' . $holiday->id,
            'type' => 'required|in:public,school,exam',
            'recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $holiday->update($validated);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $this->authorize('delete', $holiday);

        $holiday->delete();

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }
}
