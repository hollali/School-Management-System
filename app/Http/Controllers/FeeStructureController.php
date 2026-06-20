<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\SchoolClass;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage-users');

        $structures = FeeStructure::with(['schoolClass', 'items.category', 'creator'])
            ->latest()
            ->paginate(15);

        $classes = SchoolClass::orderBy('name')->get();
        $categories = FeeCategory::where('is_active', true)->get();

        return view('fee-structures.index', compact('structures', 'classes', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'grade_level' => ['nullable', 'string', 'max:100'],
            'academic_term' => ['nullable', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.fee_category_id' => ['required', 'exists:fee_categories,id'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $structure = FeeStructure::create([
            'name' => $data['name'],
            'class_id' => $data['class_id'] ?? null,
            'grade_level' => $data['grade_level'] ?? null,
            'academic_term' => $data['academic_term'] ?? null,
            'academic_year' => $data['academic_year'],
            'description' => $data['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        foreach ($data['items'] as $item) {
            FeeStructureItem::create([
                'fee_structure_id' => $structure->id,
                'fee_category_id' => $item['fee_category_id'],
                'amount' => $item['amount'],
                'description' => $item['description'] ?? null,
            ]);
        }

        ActivityLogger::log('fee-structure-created', 'FeeStructure', $structure->id,
            "Created fee structure: {$structure->name}");

        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'academic_term' => ['nullable', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.fee_category_id' => ['required', 'exists:fee_categories,id'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        $feeStructure->update([
            'name' => $data['name'],
            'class_id' => $data['class_id'] ?? $feeStructure->class_id,
            'academic_term' => $data['academic_term'] ?? $feeStructure->academic_term,
            'academic_year' => $data['academic_year'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? $feeStructure->is_active,
        ]);

        $feeStructure->items()->delete();
        foreach ($data['items'] as $item) {
            FeeStructureItem::create([
                'fee_structure_id' => $feeStructure->id,
                'fee_category_id' => $item['fee_category_id'],
                'amount' => $item['amount'],
                'description' => $item['description'] ?? null,
            ]);
        }

        ActivityLogger::log('fee-structure-updated', 'FeeStructure', $feeStructure->id,
            "Updated fee structure: {$feeStructure->name}");

        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        $this->authorize('manage-users');

        ActivityLogger::log('fee-structure-deleted', 'FeeStructure', $feeStructure->id,
            "Deleted fee structure: {$feeStructure->name}");
        $feeStructure->delete();

        return redirect()->route('fee-structures.index')
            ->with('success', 'Fee structure deleted successfully.');
    }
}
