<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::with('category');

        // Filter by category if provided
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        $companies = $query->paginate(10);
        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        // dd($request->all());
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('companies', 'public');
                $data['image'] = $imagePath;
            }


            $company = Company::create($data)->fresh(['category']);

            // Load the category relationship
            $company->load('category');

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = Company::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $data = $request->validated();

            // Handle image update
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($company->image) {
                    Storage::disk('public')->delete($company->image);
                }
                $data['image'] = $request->file('image')->store('companies', 'public');
            }

            $company->update($data);

            // Reload company with category
            $company = $company->fresh(['category']);

            return response()->json([
                'success' => true,
                'data' => new CompanyResource($company),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = Company::findOrFail($id);

            // Delete image if exists
            if ($company->image) {
                Storage::disk('public')->delete($company->image);
            }

            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }
    }
}
