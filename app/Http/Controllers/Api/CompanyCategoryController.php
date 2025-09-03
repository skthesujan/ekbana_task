<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CompanyCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCategoryResource;
use App\Http\Requests\StoreCompanyCategoryRequest;
use App\Http\Requests\UpdateCompanyCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = CompanyCategory::query();

            // Search functionality
            if ($request->has('keyword') && !empty($request->keyword)) {
                $keyword = $request->keyword;
                $query->where('title', 'like', "%{$keyword}%");
            }

            $categories = $query->paginate(10);

            return CompanyCategoryResource::collection($categories);

        } catch (\Exception $e) {
            \Log::error('Category index error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching categories.'
            ], 500);
        }
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
        try {
            $category = CompanyCategory::with([
                'companies' => function ($query) {
                    $query->where('status', true);
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'title' => $category->title,
                    'companies' => $category->companies->map(function ($company) {
                        return [
                            'id' => $company->id,
                            'title' => $company->title,
                            'image' => $company->image,
                            'description' => $company->description,
                            'status' => $company->status
                        ];
                    })
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyCategoryRequest $request, string $id)
    {
        try {
            $category = CompanyCategory::findOrFail($id);

            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'data' => new CompanyCategoryResource($category)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Category update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating the category.'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
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
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Category delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting the category.'
            ], 500);
        }
    }

}
