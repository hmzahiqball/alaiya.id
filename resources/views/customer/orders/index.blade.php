@extends('layouts.app')

@section('title', 'Pesanan Saya — alaiya.id')

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-3xl text-zinc-900">Pesanan Pre-Order Saya</h1>
                <p class="text-sm text-zinc-600 mt-1">Pantau tenggat pembayaran, status produksi, dan nomor resi pengiriman.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="px-4 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold transition">
                + Pesan Lagi
            </a>
        </div>

        <div class="space-y-4">
            @forelse($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="block bg-white rounded-2xl border border-zinc-200 p-5 hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <span class="font-display font-bold text-base text-zinc-900">{{ $order->order_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-soft text-brand">
                                    {{ $order->statusLabel() }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-600">
                                {{ $order->batch->title }} • {{ $order->items->pluck('variant_name_snapshot')->join(', ') }} ({{ $order->totalQuantity() }} pcs)
                            </div>
                            <div class="text-xs text-zinc-500">
                                Batas bayar: {{ $order->payment_deadline->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="font-display font-bold text-lg text-zinc-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <div class="text-xs font-semibold text-brand mt-1">Lihat Detail & Tracking →</div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl border border-zinc-200 p-10 text-center text-sm text-zinc-500">
                    Anda belum memiliki pesanan PO.
                </div>
            @endforelse
        </div>
    </section>
@endsection
