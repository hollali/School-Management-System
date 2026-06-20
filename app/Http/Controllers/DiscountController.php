<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Discount;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage-users');

        $discounts = Discount::with(['student.user', 'schoolClass', 'approver'])
            ->latest()
            ->paginate(15);

        $students = Student::with('user')->orderBy('id')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('discounts.index', compact('discounts', 'students', 'classes'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'type' => ['required', 'in:discount,scholarship,waiver'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'application' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        $discount = Discount::create([
            'student_id' => $data['student_id'] ?? null,
            'class_id' => $data['class_id'] ?? null,
            'type' => $data['type'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'application' => $data['application'],
            'value' => $data['value'],
            'valid_from' => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'approved_by' => Auth::id(),
        ]);

        ActivityLogger::log('discount-created', 'Discount', $discount->id,
            "Created {$data['type']}: {$data['name']} ({$data['value']}{$data['application']})");

        return redirect()->route('discounts.index')
            ->with('success', ucfirst($data['type']) . ' created successfully.');
    }

    public function update(Request $request, Discount $discount)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'type' => ['required', 'in:discount,scholarship,waiver'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'application' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['boolean'],
        ]);

        $discount->update($data);

        ActivityLogger::log('discount-updated', 'Discount', $discount->id,
            "Updated {$data['type']}: {$data['name']}");

        return redirect()->route('discounts.index')
            ->with('success', ucfirst($data['type']) . ' updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $this->authorize('manage-users');

        ActivityLogger::log('discount-deleted', 'Discount', $discount->id,
            "Deleted {$discount->type}: {$discount->name}");
        $discount->delete();

        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
}
