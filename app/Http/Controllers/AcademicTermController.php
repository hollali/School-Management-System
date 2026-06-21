<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicTermController extends Controller
{
    public function index()
    {
        $terms = AcademicTerm::orderByDesc('start_date')->paginate(15);
        return view('academic-terms.index', compact('terms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->boolean('is_current')) {
            AcademicTerm::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicTerm::create($data);

        return redirect()->route('academic-terms.index')->with('success', 'Academic term created.');
    }

    public function update(Request $request, AcademicTerm $academicTerm)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        if ($request->boolean('is_current')) {
            AcademicTerm::where('is_current', true)->update(['is_current' => false]);
        }

        $academicTerm->update($data);

        return redirect()->route('academic-terms.index')->with('success', 'Academic term updated.');
    }

    public function destroy(AcademicTerm $academicTerm)
    {
        $academicTerm->delete();
        return redirect()->route('academic-terms.index')->with('success', 'Academic term deleted.');
    }
}
