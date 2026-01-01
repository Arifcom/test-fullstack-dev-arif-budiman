<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product Routes
Route::get('/products', App\Livewire\ProductIndex::class)->name('products.index');
Route::get('/products/create', App\Livewire\ProductCreate::class)->name('products.create');
Route::get('/products/{id}/edit', App\Livewire\ProductEdit::class)->name('products.edit');
