<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Holiday::class, 'holiday');
    }

    public function index()
    {
        $holidays = Holiday::orderBy('holiday_date', 'desc')->paginate(20);
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
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
        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
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
        $holiday->delete();

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }
}
