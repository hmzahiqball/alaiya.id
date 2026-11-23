<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->with(['activeVariants', 'latestBatch'])
            ->orderBy('id')
            ->take(4)
            ->get();

        return view('public.home', compact('featuredProducts'));
    }

    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();

        $products = Product::query()
            ->where('is_active', true)
            ->when($category !== '' && $category !== 'Semua', fn ($q) => $q->where('category', $category))
            ->with(['activeVariants', 'latestBatch'])
            ->orderBy('id')
            ->get();

        $categories = Product::query()
            ->where('is_active', true)
            ->distinct()
            ->pluck('category');

        return view('public.catalog', compact('products', 'categories', 'category'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['activeVariants', 'latestBatch']);
        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->with(['latestBatch'])
            ->take(3)
            ->get();

        return view('public.product-detail', compact('product', 'relatedProducts'));
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function guide(): View
    {
        return view('public.guide');
    }
}
