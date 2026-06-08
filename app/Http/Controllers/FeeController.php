<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $fees = Fee::latest()->paginate(15);

        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        return view('fees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        Fee::create($data);

        return redirect()->route('fees.index')->with('success', 'Fee record saved successfully.');
    }

    public function edit(Fee $fee)
    {
        return view('fees.edit', compact('fee'));
    }

    public function update(Request $request, Fee $fee)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $fee->update($data);

        return redirect()->route('fees.index')->with('success', 'Fee record updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Fee record deleted successfully.');
    }
}
