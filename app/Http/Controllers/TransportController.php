<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transports = Transport::latest()->paginate(15);

        return view('transports.index', compact('transports'));
    }

    public function create()
    {
        return view('transports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_number' => ['required', 'string', 'max:255'],
            'route' => ['required', 'string', 'max:255'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Transport::create($data);

        return redirect()->route('transports.index')->with('success', 'Transport record saved successfully.');
    }

    public function edit(Transport $transport)
    {
        return view('transports.edit', compact('transport'));
    }

    public function update(Request $request, Transport $transport)
    {
        $data = $request->validate([
            'vehicle_number' => ['required', 'string', 'max:255'],
            'route' => ['required', 'string', 'max:255'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $transport->update($data);

        return redirect()->route('transports.index')->with('success', 'Transport record updated successfully.');
    }

    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect()->route('transports.index')->with('success', 'Transport record deleted successfully.');
    }
}
