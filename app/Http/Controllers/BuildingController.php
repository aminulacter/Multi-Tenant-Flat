<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use App\Models\HouseOwner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BuildingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Building::class);
        
        return Inertia::render('Building/index');
    }

    /**
     * Get buildings for datatable
     */
    public function getBuildings()
    {
        $this->authorize('viewAny', Building::class);
        
        $buildings = Building::with('houseOwner')->paginate(10);
        return response()->json(['data' => BuildingResource::collection($buildings), 'pagination' => [
            'total' => $buildings->total(),
            'per_page' => $buildings->perPage(),
            'current_page' => $buildings->currentPage(),
            'last_page' => $buildings->lastPage(),
            'from' => $buildings->firstItem(),
            'to' => $buildings->lastItem(),]]);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Building::class);
        
        $houseOwners = HouseOwner::all();
        
        return Inertia::render('Building/create', [
            'houseOwners' => $houseOwners,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request)
    {
        $this->authorize('create', Building::class);
        
        try {
            DB::beginTransaction();

            $building = Building::create($request->validated());

            DB::commit();

            return redirect()->route('buildings.index')->with('success', 'Building created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Building creation failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building)
    {
        $this->authorize('view', $building);
        
        $building->load('houseOwner');
        return Inertia::render('Building/show', [
            'building' => new BuildingResource($building),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building)
    {
        $this->authorize('update', $building);
        
        $building->load('houseOwner');
        $houseOwners = HouseOwner::all();
        
        return Inertia::render('Building/edit', [
            'building' => new BuildingResource($building),
            'houseOwners' => $houseOwners,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $this->authorize('update', $building);
        
        try {
            DB::beginTransaction();

            $building->update($request->validated());

            DB::commit();

            return redirect()->route('buildings.index')->with('success', 'Building updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update building: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building)
    {
        $this->authorize('delete', $building);
        
        try {
            // Check if building has flats
            if ($building->flats()->count() > 0) {
                return back()->with('error', 'Cannot delete building. It has associated flats. Please remove all flats first.');
            }
            
            $building->delete();
            return redirect()->route('buildings.index')->with('success', 'Building deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete building: ' . $e->getMessage());
        }
    }
}
