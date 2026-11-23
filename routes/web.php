<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/tentang', [CatalogController::class, 'about'])->name('about');
Route::get('/panduan', [CatalogController::class, 'guide'])->name('guide');

// Stubs for navigation links until Steps 4 & 5
Route::view('/masuk', 'public.home')->name('login');
Route::post('/keluar', fn () => redirect()->route('home'))->name('logout');
Route::view('/profil', 'public.home')->name('profile.edit');
Route::view('/keranjang', 'public.home')->name('checkout.index');
Route::post('/keranjang/tambah', fn () => redirect()->route('catalog.index'))->name('checkout.add');
Route::view('/pesanan-saya', 'public.home')->name('orders.index');
Route::view('/live-chat', 'public.home')->name('chat.index');
Route::view('/admin/produk', 'public.home')->name('admin.products.index');
Route::view('/admin/pesanan', 'public.home')->name('admin.orders.index');
Route::view('/admin/chat', 'public.home')->name('admin.chat.index');
Route::view('/admin/laporan', 'public.home')->name('admin.reports.index');
