<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductBatchController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ChatController;
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

    Route::get('/live-chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/live-chat', [ChatController::class, 'send'])->name('chat.send');
});

// Admin operational routes (BR-01)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/produk', [ProductBatchController::class, 'index'])->name('products.index');
    Route::post('/produk', [ProductBatchController::class, 'storeProduct'])->name('products.store');
    Route::patch('/produk/{product}/toggle', [ProductBatchController::class, 'toggleProduct'])->name('products.toggle');
    Route::post('/batch', [ProductBatchController::class, 'storeBatch'])->name('batches.store');
    Route::patch('/batch/{batch}', [ProductBatchController::class, 'updateBatch'])->name('batches.update');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/pesanan/{order}/pembayaran/{payment}/setujui', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('/pesanan/{order}/pembayaran/{payment}/tolak', [AdminOrderController::class, 'reject'])->name('orders.reject');
    Route::post('/pesanan/{order}/status', [AdminOrderController::class, 'advance'])->name('orders.advance');
    Route::post('/pesanan/{order}/batal', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('chat.index');
    Route::post('/chat/{conversation}', [ChatController::class, 'adminSend'])->name('chat.send');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/ekspor-penjualan', [ReportController::class, 'exportSales'])->name('reports.export-sales');
    Route::get('/laporan/ekspor-kuota', [ReportController::class, 'exportQuota'])->name('reports.export-quota');
});
