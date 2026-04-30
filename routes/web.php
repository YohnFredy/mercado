<?php

use App\Livewire\Checkout\CheckoutConfirmation;
use App\Livewire\Checkout\CheckoutShipping;
use App\Livewire\Checkout\CheckoutSummary;
use Illuminate\Support\Facades\Route;

/* Route::view('/', 'welcome')->name('home'); */

// Home page
Route::livewire('/inicio', 'pages::home')->name('home');

// Store (former home)
Route::livewire('/', 'pages::product.product-list')->name('tienda');

Route::livewire('/category/{category:slug}', 'pages::product.product-list')->name('category');
Route::livewire('/p/{product:slug}', 'pages::product.product-detail')->name('product.show');

Route::livewire('/checkout', CheckoutSummary::class)->name('checkout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/mis-pedidos', 'pages::customer.orders.order-history')->name('customer.orders');

    Route::livewire('/checkout/shipping', CheckoutShipping::class)->name('checkout.shipping');
    Route::livewire('/checkout/confirmation/{order}', CheckoutConfirmation::class)->name('checkout.confirmation');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::livewire('dashboard', 'pages::admin.⚡dashboard')->name('dashboard')->middleware('permission:dashboard:view');
        Route::livewire('orders', 'pages::admin.orders.order-manager')->name('orders')->middleware('permission:orders:view');
        Route::livewire('categories', 'pages::admin.categories.category-manager')->name('categories')->middleware('permission:categories:view');
        Route::livewire('brands', 'pages::admin.brands.brand-manager')->name('brands')->middleware('permission:brands:view');
        Route::livewire('products', 'pages::admin.products.product-manager')->name('products')->middleware('permission:products:view');

        // Security & User Management
        Route::prefix('security')->name('security.')->group(function () {
            Route::livewire('roles', 'pages::admin.security.role-manager')->name('roles')->middleware('permission:roles:view');
            Route::livewire('users', 'pages::admin.security.user-manager')->name('users')->middleware('permission:users:view');
        });
    });
});

require __DIR__ . '/settings.php';
