<?php

namespace App\Http\Controllers;

use App\Models\PoBatch;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        $batch = $cart['batch_id'] ? PoBatch::with('product')->find($cart['batch_id']) : null;
        $subtotal = collect($cart['items'])->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
        $shippingCost = empty($cart['items']) ? 0 : OrderService::FLAT_SHIPPING_COST;

        return view('customer.checkout', [
            'cart' => $cart,
            'batch' => $batch,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'user' => $request->user(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('openBatch')->findOrFail($data['product_id']);
        $batch = $product->openBatch;

        if (!$batch || !$batch->isAcceptingOrders()) {
            return back()->with('error', 'Batch PO untuk produk ini sedang ditutup.');
        }

        $variant = ProductVariant::where('id', $data['variant_id'])
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->firstOrFail();

        $cart = session('cart', ['batch_id' => null, 'items' => []]);

        // BR-06: One order contains variants from one batch
        if ($cart['batch_id'] && (int) $cart['batch_id'] !== $batch->id) {
            $cart = ['batch_id' => $batch->id, 'items' => []];
        }

        $cart['batch_id'] = $batch->id;
        $existingQty = $cart['items'][$variant->id]['quantity'] ?? 0;
        $newQty = $existingQty + (int) $data['quantity'];

        $totalInCart = collect($cart['items'])->except($variant->id)->sum('quantity') + $newQty;
        if ($totalInCart > $batch->remainingQuota()) {
            return back()->with('error', 'Jumlah pesanan melebihi sisa kuota batch (' . $batch->remainingQuota() . ' pcs).');
        }

        $cart['items'][$variant->id] = [
            'variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_name' => $variant->color_name,
            'color_hex' => $variant->color_hex,
            'unit_price' => $product->price,
            'quantity' => $newQty,
        ];

        session(['cart' => $cart]);

        return redirect()->route('checkout.index')->with('success', 'Item ditambahkan ke keranjang PO.');
    }

    public function remove(int $variantId): RedirectResponse
    {
        $cart = session('cart', ['batch_id' => null, 'items' => []]);
        unset($cart['items'][$variantId]);
        if (empty($cart['items'])) {
            session()->forget('cart');
        } else {
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $shipping = $request->validate([
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'postal_code' => ['required', 'string', 'max:20'],
            'courier' => ['required', 'string', 'max:60'],
        ]);

        $cart = session('cart', []);
        $order = $orderService->createFromCart($request->user(), $cart, $shipping);
        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', "Pesanan {$order->order_number} berhasil dibuat! Silakan unggah bukti pembayaran dalam 24 jam.");
    }
}
