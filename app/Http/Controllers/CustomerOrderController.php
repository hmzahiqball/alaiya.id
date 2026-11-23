<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['batch.product', 'items'])
            ->orderByDesc('id')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['batch.product', 'items', 'payments', 'histories.actor']);

        return view('customer.orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'local');
        $orderService->uploadProof($order, $request->user(), $path);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function proofFile(Request $request, Payment $payment): StreamedResponse
    {
        $payment->load('order');
        $user = $request->user();
        abort_unless($user->isAdmin() || $payment->order->user_id === $user->id, 403);
        abort_unless(Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path);
    }
}
