<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillCategoryRequest;
use App\Http\Requests\UpdateBillCategoryRequest;
use App\Http\Resources\BillCategoryResource;
use App\Models\BillCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BillCategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', BillCategory::class);
        
        $user = Auth::user();
        $houseOwner = $user->houseOwner;
        if (!$houseOwner) {
            abort(403, 'House owner profile not found.');
        }

        $billCategories = BillCategory::where('building_owner_id', $houseOwner->id)
            ->paginate(10);

        return Inertia::render('BillCategory/index', [
            'billCategories' => BillCategoryResource::collection($billCategories),
        ]);
    }

    /**
     * Get bill categories for datatable
     */
    public function getBillCategories()
    {
        $this->authorize('viewAny', BillCategory::class);
        
        $user = Auth::user();
        $houseOwner = $user->houseOwner;
        if (!$houseOwner) {
            abort(403, 'House owner profile not found.');
        }

        $billCategories = BillCategory::where('building_owner_id', $houseOwner->id)
            ->paginate(10);

        return response()->json([
            'data' => BillCategoryResource::collection($billCategories),
            'pagination' => [
                'total' => $billCategories->total(),
                'per_page' => $billCategories->perPage(),
                'current_page' => $billCategories->currentPage(),
                'last_page' => $billCategories->lastPage(),
                'from' => $billCategories->firstItem(),
                'to' => $billCategories->lastItem(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', BillCategory::class);
        
        return Inertia::render('BillCategory/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillCategoryRequest $request)
    {
        $this->authorize('create', BillCategory::class);
        
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $houseOwner = $user->houseOwner;
            
            if (!$houseOwner) {
                return back()->with('error', 'House owner profile not found.');
            }

            $data = $request->validated();
            $data['building_owner_id'] = $houseOwner->id;

            $billCategory = BillCategory::create($data);

            DB::commit();

            return redirect()->route('bill-categories.index')->with('success', 'Bill category created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bill category creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BillCategory $billCategory)
    {
        $this->authorize('view', $billCategory);

        return Inertia::render('BillCategory/show', [
            'billCategory' => new BillCategoryResource($billCategory),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillCategory $billCategory)
    {
        $this->authorize('update', $billCategory);

        return Inertia::render('BillCategory/edit', [
            'billCategory' => new BillCategoryResource($billCategory),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBillCategoryRequest $request, BillCategory $billCategory)
    {
        $this->authorize('update', $billCategory);
        
        try {
            DB::beginTransaction();

            $billCategory->update($request->validated());

            DB::commit();

            return redirect()->route('bill-categories.index')->with('success', 'Bill category updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update bill category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillCategory $billCategory)
    {
        $this->authorize('delete', $billCategory);
        
        try {

            // Check if bill category has associated bills
            if ($billCategory->bills()->count() > 0) {
                return back()->with('error', 'Cannot delete bill category. It has associated bills. Please remove all bills first.');
            }

            $billCategory->delete();
            return redirect()->route('bill-categories.index')->with('success', 'Bill category deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete bill category: ' . $e->getMessage());
        }
    }
}
