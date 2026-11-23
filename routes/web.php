<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/tentang', [CatalogController::class, 'about'])->name('about');
Route::get('/panduan', [CatalogController::class, 'guide'])->name('guide');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.post');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.post');
    Route::post('/masuk-demo', [AuthController::class, 'demoLogin'])->name('login.demo');
});
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profil', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/keranjang', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/keranjang/tambah', [CheckoutController::class, 'add'])->name('checkout.add');
    Route::get('/keranjang/hapus/{variantId}', [CheckoutController::class, 'remove'])->name('checkout.remove');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan-saya', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan-saya/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan-saya/{order}/bukti', [CustomerOrderController::class, 'uploadProof'])->name('orders.proof');
    Route::get('/pembayaran/{payment}/bukti', [CustomerOrderController::class, 'proofFile'])->name('payments.proof');
});

// Temporary stubs for Step 5 (Chat & Admin)
Route::view('/live-chat', 'public.home')->middleware('auth')->name('chat.index');
Route::view('/admin/produk', 'public.home')->middleware(['auth', 'admin'])->name('admin.products.index');
Route::view('/admin/pesanan', 'public.home')->middleware(['auth', 'admin'])->name('admin.orders.index');
Route::view('/admin/chat', 'public.home')->middleware(['auth', 'admin'])->name('admin.chat.index');
Route::view('/admin/laporan', 'public.home')->middleware(['auth', 'admin'])->name('admin.reports.index');
