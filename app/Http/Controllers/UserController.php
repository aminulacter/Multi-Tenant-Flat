<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\HouseOwner;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        return Inertia::render('User/index');
    }

    public function getUsers()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with('role')->paginate(10);
        //return UserResource::collection($users);
        return response()->json(['data' => UserResource::collection($users), 'pagination' => [
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'from' => $users->firstItem(),
            'to' => $users->lastItem(),]]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
       $roles = RoleResource::collection(Role::all())->resolve();
       $houseOwners = HouseOwner::with('user')->get();
       return Inertia::render('User/create', [
           'roles' => $roles,
           'houseOwners' => $houseOwners
       ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        
        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            // Get the role to determine what additional data to create
            $role = Role::find($request->role_id);

            // Create role-specific records
            if ($role && $role->name === 'House Owner' && $request->has('house_owner')) {
                HouseOwner::create([
                    'user_id' => $user->id,
                    'name' => $request->house_owner['name'],
                    'email' => $request->house_owner['email'],
                    'address' => $request->house_owner['address'] ?? null,
                    'city' => $request->house_owner['city'] ?? null,
                    'zip' => $request->house_owner['zip'] ?? null,
                ]);
            } elseif ($role && $role->name === 'Tenant' && $request->has('tenant')) {
                Tenant::create([
                    'user_id' => $user->id,
                    'house_owner_id' => $request->tenant['house_owner_id'] ?? null,
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->tenant['address'] ?? null,
                    'city' => $request->tenant['city'] ?? null,
                    'zip' => $request->tenant['zip'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'User creation failed');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $user->load(['role', 'houseOwner', 'tenant']);
        $roles = RoleResource::collection(Role::all())->resolve();
        $houseOwners = HouseOwner::with('user')->get();
        
        return Inertia::render('User/edit', [
            'user' => (new UserResource($user))->resolve(),
            'roles' => $roles,
            'houseOwners' => $houseOwners
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        try {
            DB::beginTransaction();

            // Update the user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Get the role to determine what additional data to update
            $role = Role::find($request->role_id);

            // Update role-specific records
            if ($role && $role->name === 'House Owner' && $request->has('house_owner')) {
                $houseOwner = $user->houseOwner;
                if ($houseOwner) {
                    $houseOwner->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->house_owner['address'] ?? null,
                        'city' => $request->house_owner['city'] ?? null,
                        'zip' => $request->house_owner['zip'] ?? null,
                    ]);
                } else {
                    HouseOwner::create([
                        'user_id' => $user->id,
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->house_owner['address'] ?? null,
                        'city' => $request->house_owner['city'] ?? null,
                        'zip' => $request->house_owner['zip'] ?? null,
                    ]);
                }
            } elseif ($role && $role->name === 'Tenant' && $request->has('tenant')) {
                $tenant = $user->tenant;
                if ($tenant) {
                    $tenant->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->tenant['address'] ?? null,
                        'city' => $request->tenant['city'] ?? null,
                        'zip' => $request->tenant['zip'] ?? null,
                        'house_owner_id' => $request->tenant['house_owner_id'] ?? null,
                    ]);
                } else {
                    Tenant::create([
                        'user_id' => $user->id,
                        'house_owner_id' => $request->tenant['house_owner_id'] ?? null,
                        'name' => $request->name,
                        'email' => $request->email,
                        'address' => $request->tenant['address'] ?? null,
                        'city' => $request->tenant['city'] ?? null,
                        'zip' => $request->tenant['zip'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        try{
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'User deletion failed');
        }
    }
}
