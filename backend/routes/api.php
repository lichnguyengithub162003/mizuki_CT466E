<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\Catalog\BrandController;
use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Customer\ProfileController;
use App\Http\Controllers\Api\V1\LocationController;


Route::prefix('v1')->name('api.v1.')->group(function (): void {

    // Public catalog routes
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/{slug}', [BrandController::class, 'show'])->name('brands.show');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    // Auth routes
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('register', [CustomerAuthController::class, 'register'])
            ->middleware('throttle:auth.register')
            ->name('register');
        Route::post('login', [CustomerAuthController::class, 'login'])
            ->middleware('throttle:auth.login')
            ->name('login');
        Route::get('me', [CustomerAuthController::class, 'me'])
            ->middleware('auth:sanctum')
            ->name('me');
        Route::post('logout', [CustomerAuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
        Route::post('forgot-password', [CustomerAuthController::class, 'forgotPassword'])
            ->middleware('throttle:auth.register')
            ->name('forgot-password');
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword'])
            ->middleware('throttle:auth.register')
            ->name('reset-password');
        Route::get('google/redirect', [GoogleAuthController::class, 'redirect'])
            ->middleware('throttle:auth.login')
            ->name('google.redirect');
        Route::get('google/callback', [GoogleAuthController::class, 'callback'])
            ->middleware('throttle:auth.login')
            ->name('google.callback');
    });

    // Customer routes
    Route::prefix('customer')->name('customer.')->middleware('auth:sanctum')->group(function (): void {
        // Profile
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
        Route::patch('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

        // Addresses
        Route::get('addresses', [ProfileController::class, 'indexAddress'])->name('addresses.index');
        Route::post('addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
        Route::patch('addresses/{id}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('addresses/{id}', [ProfileController::class, 'destroyAddress'])->name('addresses.destroy');
        Route::patch('addresses/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('addresses.set-default');
    });

    //Location routes
    Route::prefix('locations')->name('locations.')->group(function (): void {
        Route::get('provinces', [LocationController::class, 'provinces'])->name('provinces');
        Route::get('provinces/{provinceId}/districts', [LocationController::class, 'districts'])->name('districts');
        Route::get('districts/{districtId}/wards', [LocationController::class, 'wards'])->name('wards');
    });
});
