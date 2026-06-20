<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\FeeCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage-users');

        $categories = FeeCategory::latest()->paginate(15);
        return view('fee-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:fee_categories,code'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = FeeCategory::create($data);

        ActivityLogger::log('fee-category-created', 'FeeCategory', $category->id,
            "Created fee category: {$category->name}");

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category created successfully.');
    }

    public function update(Request $request, FeeCategory $feeCategory)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:fee_categories,code,' . $feeCategory->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $feeCategory->update($data);

        ActivityLogger::log('fee-category-updated', 'FeeCategory', $feeCategory->id,
            "Updated fee category: {$feeCategory->name}");

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category updated successfully.');
    }

    public function destroy(FeeCategory $feeCategory)
    {
        $this->authorize('manage-users');

        ActivityLogger::log('fee-category-deleted', 'FeeCategory', $feeCategory->id,
            "Deleted fee category: {$feeCategory->name}");
        $feeCategory->delete();

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category deleted successfully.');
    }
}
