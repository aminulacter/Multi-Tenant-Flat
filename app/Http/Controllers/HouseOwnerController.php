<?php

namespace App\Http\Controllers;

use App\Models\HouseOwner;
use Illuminate\Http\Request;

class HouseOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
             $houseOwners = HouseOwner::all();
             return inertia('HouseOwner/Index', compact('houseOwners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HouseOwner $houseOwner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HouseOwner $houseOwner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HouseOwner $houseOwner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HouseOwner $houseOwner)
    {
        //
    }
}
