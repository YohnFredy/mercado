<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/* Route::view('/', 'welcome')->name('home'); */

Route::livewire('/', 'pages::product.product-list')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::redirect('/', '/admin/categories');
        Route::livewire('categories', 'pages::admin.categories.category-manager')->name('categories');
        Route::livewire('brands', 'pages::admin.brands.brand-manager')->name('brands');
        Route::livewire('products', 'pages::admin.products.product-manager')->name('products');
    });
});



require __DIR__ . '/settings.php';
