<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlatRequest;
use App\Http\Requests\UpdateFlatRequest;
use App\Http\Resources\FlatResource;
use App\Models\Building;
use App\Models\Flat;
use App\Models\Tenant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FlatController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Flat::class);
        
        $user = Auth::user();
        
        // Get flats based on user role
        if ($user->role_id === 1) { // SuperAdmin - can see all flats
            $flats = Flat::with(['building', 'tenant', 'houseOwner'])->paginate(10);
        } elseif ($user->role_id === 2) { // House Owner - can only see their own flats
            $houseOwner = $user->houseOwner;
            if ($houseOwner) {
                $flats = Flat::with(['building', 'tenant', 'houseOwner'])
                    ->where('house_owner_id', $houseOwner->id)
                    ->paginate(10);
            } else {
                $flats = collect()->paginate(10);
            }
        } else {
            // Other roles - no access
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('Flat/index', [
            'flats' => FlatResource::collection($flats),
        ]);
    }

    /**
     * Get available tenants (without flats) for a specific house owner
     */
    public function getAvailableTenants(Request $request)
    {
        $this->authorize('viewAny', Flat::class);
        
        $user = Auth::user();
        $houseOwnerId = $request->get('house_owner_id');
        
        if ($user->role_id === 1) { // SuperAdmin - can see all, but filter by house owner if provided
            $query = Tenant::with('user')->whereDoesntHave('flats');
            if ($houseOwnerId) {
                $query->where('house_owner_id', $houseOwnerId);
            }
            $tenants = $query->get();
        } elseif ($user->role_id === 2) { // House Owner - can only see their own
            $houseOwner = $user->houseOwner;
            if ($houseOwner) {
                $tenants = Tenant::with('user')
                    ->where('house_owner_id', $houseOwner->id)
                    ->whereDoesntHave('flats')
                    ->get();
            } else {
                $tenants = collect();
            }
        } else {
            abort(403, 'Unauthorized access');
        }

        return response()->json($tenants);
    }

    /**
     * Get flats for datatable
     */
    public function getFlats()
    {
        $this->authorize('viewAny', Flat::class);
        
        $user = Auth::user();
        
        // Get flats based on user role
        if ($user->role_id === 1) { // SuperAdmin - can see all flats
            $flats = Flat::with(['building', 'tenant', 'houseOwner'])->paginate(10);
        } elseif ($user->role_id === 2) { // House Owner - can only see their own flats
            $houseOwner = $user->houseOwner;
            if ($houseOwner) {
                $flats = Flat::with(['building', 'tenant', 'houseOwner'])
                    ->where('house_owner_id', $houseOwner->id)
                    ->paginate(10);
            } else {
                $flats = collect()->paginate(10);
            }
        } else {
            // Other roles - no access
            abort(403, 'Unauthorized access');
        }

        return response()->json([
            'data' => FlatResource::collection($flats),
            'pagination' => [
                'total' => $flats->total(),
                'per_page' => $flats->perPage(),
                'current_page' => $flats->currentPage(),
                'last_page' => $flats->lastPage(),
                'from' => $flats->firstItem(),
                'to' => $flats->lastItem(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Flat::class);
        
        $user = Auth::user();
        
        // Get buildings and tenants based on user role
        if ($user->role_id === 1) { // SuperAdmin - can see all buildings, but tenants should be filtered by building's house owner
            $buildings = Building::with('houseOwner')->get();
            // For SuperAdmin, we need to get tenants based on the buildings they can manage
            // This will be handled in the frontend by filtering based on selected building
            $tenants = Tenant::with('user')->whereDoesntHave('flats')->get();
        } elseif ($user->role_id === 2) { // House Owner - can only see their own
            $houseOwner = $user->houseOwner;
            if ($houseOwner) {
                $buildings = Building::with('houseOwner')
                    ->where('house_owner_id', $houseOwner->id)
                    ->get();
                $tenants = Tenant::with('user')
                    ->where('house_owner_id', $houseOwner->id)
                    ->whereDoesntHave('flats')
                    ->get();
            } else {
                $buildings = collect();
                $tenants = collect();
            }
        } else {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('Flat/create', [
            'buildings' => $buildings,
            'tenants' => $tenants,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFlatRequest $request)
    {
        $this->authorize('create', Flat::class);
        
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $data = $request->validated();

            // Add house_owner_id based on user role
            if ($user->role_id === 2) { // House Owner
                $houseOwner = $user->houseOwner;
                if (!$houseOwner) {
                    return back()->with('error', 'House owner profile not found');
                }
                $data['house_owner_id'] = $houseOwner->id;
            }

            $flat = Flat::create($data);

            DB::commit();

            return redirect()->route('flats.index')->with('success', 'Flat created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Flat creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Flat $flat)
    {
        $this->authorize('view', $flat);

        $flat->load(['building', 'tenant', 'houseOwner']);
        return Inertia::render('Flat/show', [
            'flat' => new FlatResource($flat),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Flat $flat)
    {
        $this->authorize('update', $flat);
        
        $user = Auth::user();

        // Get buildings and tenants based on user role
        if ($user->role_id === 1) { // SuperAdmin - can see all
            $buildings = Building::with('houseOwner')->get();
            // Get tenants without flats OR the current tenant of this flat
            $tenants = Tenant::with('user')
                ->where(function($query) use ($flat) {
                    $query->whereDoesntHave('flats')
                          ->orWhere('id', $flat->tenant_id);
                })
                ->get();
        } elseif ($user->role_id === 2) { // House Owner - can only see their own
            $houseOwner = $user->houseOwner;
            if ($houseOwner) {
                $buildings = Building::with('houseOwner')
                    ->where('house_owner_id', $houseOwner->id)
                    ->get();
                // Get tenants without flats OR the current tenant of this flat
                $tenants = Tenant::with('user')
                    ->where('house_owner_id', $houseOwner->id)
                    ->where(function($query) use ($flat) {
                        $query->whereDoesntHave('flats')
                              ->orWhere('id', $flat->tenant_id);
                    })
                    ->get();
            } else {
                $buildings = collect();
                $tenants = collect();
            }
        } else {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('Flat/edit', [
            'flat' => new FlatResource($flat),
            'buildings' => $buildings,
            'tenants' => $tenants,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFlatRequest $request, Flat $flat)
    {
        $this->authorize('update', $flat);
        
        try {
            DB::beginTransaction();

            $flat->update($request->validated());

            DB::commit();

            return redirect()->route('flats.index')->with('success', 'Flat updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update flat: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Flat $flat)
    {
        $this->authorize('delete', $flat);
        
        try {

            $flat->delete();
            return redirect()->route('flats.index')->with('success', 'Flat deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete flat: ' . $e->getMessage());
        }
    }

    /**
     * Allocate tenant to flat
     */
    public function allocateTenant(Request $request, Flat $flat)
    {
        $this->authorize('allocateTenant', $flat);
        
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        // Check if tenant already has a flat
        $tenant = Tenant::find($request->tenant_id);
        if ($tenant && $tenant->flats()->count() > 0) {
            return back()->with('error', 'This tenant is already allocated to a flat.');
        }

        try {
            DB::beginTransaction();

            $flat->update(['tenant_id' => $request->tenant_id]);

            DB::commit();

            return redirect()->route('flats.index')->with('success', 'Tenant allocated to flat successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to allocate tenant: ' . $e->getMessage());
        }
    }

    /**
     * Remove tenant from flat
     */
    public function removeTenant(Flat $flat)
    {
        $this->authorize('removeTenant', $flat);
        
        try {
            DB::beginTransaction();

            $flat->update(['tenant_id' => null]);

            DB::commit();

            return redirect()->route('flats.index')->with('success', 'Tenant removed from flat successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove tenant: ' . $e->getMessage());
        }
    }
}
