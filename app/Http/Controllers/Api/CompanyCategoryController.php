<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CompanyCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCategoryResource;
use App\Http\Requests\StoreCompanyCategoryRequest;
use App\Http\Requests\UpdateCompanyCategoryRequest;

class CompanyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CompanyCategory::query();

        // Search functionality
        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $query->where('title', 'like', "%{$keyword}%");
        }

        $categories = $query->paginate(10);
        return CompanyCategoryResource::collection($categories);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyCategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $category = CompanyCategory::create($validated);

            return response()->json([
                'success' => true,
                'data' => new CompanyCategoryResource($category)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = CompanyCategory::with('companies')->findOrFail($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        return new CompanyCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyCategoryRequest $request, string $id)
    {
        $category = CompanyCategory::findOrFail($id);
        $category->update($request->validated());
        return response()->json([
            'success' => true,
            'data' => new CompanyCategoryResource($category)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = CompanyCategory::findOrFail($id);
        // Check if category has companies
        if ($category->companies()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated companies'
            ], 422);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
