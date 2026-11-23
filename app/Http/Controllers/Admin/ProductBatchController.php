<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoBatch;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductBatchController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['variants', 'batches'])
            ->orderByDesc('id')
            ->get();

        return view('admin.products', compact('products'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:1000'],
            'material' => ['required', 'string', 'max:150'],
            'dimensions' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'colors' => ['required', 'string', 'max:300'],
        ]);

        $product = Product::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(4)),
            'category' => $data['category'],
            'price' => $data['price'],
            'material' => $data['material'],
            'dimensions' => $data['dimensions'],
            'description' => $data['description'],
            'image_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80',
            'is_active' => true,
        ]);

        foreach (array_filter(array_map('trim', explode(',', $data['colors']))) as $color) {
            ProductVariant::create([
                'product_id' => $product->id,
                'color_name' => $color,
                'color_hex' => '#2D5A4C',
                'is_active' => true,
            ]);
        }

        return back()->with('success', "Master produk '{$product->name}' berhasil ditambahkan.");
    }

    public function toggleProduct(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', 'Status publikasi produk diperbarui.');
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'batch_code' => ['required', 'string', 'max:40'],
            'title' => ['required', 'string', 'max:150'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'quota_total' => ['required', 'integer', 'min:1'],
            'production_estimate' => ['required', 'string', 'max:150'],
        ]);

        // BR-02 / AC-06: Only one open batch per product at the same time
        $hasOpen = PoBatch::where('product_id', $data['product_id'])
            ->where('status', 'open')
            ->exists();

        if ($hasOpen) {
            return back()->with('error', 'Produk ini sudah memiliki batch PO yang berstatus Terbuka (BR-02 / AC-06).');
        }

        PoBatch::create(array_merge($data, [
            'allocated_units' => 0,
            'status' => 'open',
        ]));

        return back()->with('success', "Batch {$data['batch_code']} berhasil dibuka.");
    }

    public function updateBatch(Request $request, PoBatch $batch): RedirectResponse
    {
        $data = $request->validate([
            'quota_total' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:scheduled,open,closed'],
        ]);

        // BR-03 / AC-06: Active quota cannot be lowered below allocated units
        if ((int) $data['quota_total'] < $batch->allocated_units) {
            return back()->with('error', "Kuota tidak boleh diturunkan di bawah jumlah unit teralokasi ({$batch->allocated_units} pcs).");
        }

        if ($data['status'] === 'open') {
            $otherOpen = PoBatch::where('product_id', $batch->product_id)
                ->where('id', '!=', $batch->id)
                ->where('status', 'open')
                ->exists();
            if ($otherOpen) {
                return back()->with('error', 'Sudah ada batch terbuka lain untuk produk ini.');
            }
        }

        $batch->update($data);

        return back()->with('success', "Batch {$batch->batch_code} berhasil diperbarui.");
    }
}
