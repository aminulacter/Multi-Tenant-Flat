<?php

use App\Http\Controllers\BillCategoryController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\FlatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// SuperAdmin only routes
Route::middleware(['superadmin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('getusers', [UserController::class, 'getUsers'])->name('getusers');
    
    Route::resource('buildings', BuildingController::class);
    Route::get('getbuildings', [BuildingController::class, 'getBuildings'])->name('getbuildings');
});

// SuperAdmin and House Owner routes
Route::middleware(['auth'])->group(function () {
    Route::resource('flats', FlatController::class);
    Route::get('getflats', [FlatController::class, 'getFlats'])->name('getflats');
    Route::get('getavailabletenants', [FlatController::class, 'getAvailableTenants'])->name('getavailabletenants');
    Route::post('flats/{flat}/allocate-tenant', [FlatController::class, 'allocateTenant'])->name('flats.allocate-tenant');
    Route::delete('flats/{flat}/remove-tenant', [FlatController::class, 'removeTenant'])->name('flats.remove-tenant');
});

// House Owner only routes
Route::middleware(['auth'])->group(function () {
    Route::resource('bill-categories', BillCategoryController::class);
    Route::get('getbillcategories', [BillCategoryController::class, 'getBillCategories'])->name('getbillcategories');
});
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
