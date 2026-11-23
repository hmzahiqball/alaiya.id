@extends('layouts.app')

@section('title', 'Tracking ' . $order->order_number . ' — alaiya.id')

@section('content')
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-brand hover:underline">← Kembali ke Daftar Pesanan</a>
                <h1 class="font-display font-bold text-3xl text-zinc-900 mt-1">Pesanan {{ $order->order_number }}</h1>
                <p class="text-sm text-zinc-600">{{ $order->batch->title }} • Dibuat {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold bg-brand text-white self-start sm:self-auto">
                {{ $order->statusLabel() }}
            </span>
        </div>

        {{-- 6-Step Progress Bar --}}
        @php
            $steps = [
                'menunggu_pembayaran' => '1. Menunggu Pembayaran',
                'menunggu_verifikasi' => '2. Menunggu Verifikasi',
                'dibayar' => '3. Dibayar',
                'diproduksi' => '4. Diproduksi',
                'dikirim' => '5. Dikirim',
                'selesai' => '6. Selesai',
            ];
            $keys = array_keys($steps);
            $currentIdx = array_search($order->status, $keys, true);
        @endphp
        <div class="bg-white rounded-2xl border border-zinc-200 p-5 overflow-x-auto">
            <div class="flex items-center justify-between gap-2 min-w-[640px]">
                @foreach($steps as $k => $label)
                    @php
                        $idx = array_search($k, $keys, true);
                        $active = $currentIdx !== false && $idx <= $currentIdx;
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $active ? 'bg-brand text-white' : 'bg-zinc-100 text-zinc-500' }}">
                        {{ $label }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7 space-y-6">
                {{-- Payment Upload Box --}}
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                    <h2 class="font-display font-bold text-lg text-zinc-900">Pembayaran Manual & Unggah Bukti</h2>
                    <div class="rounded-xl bg-zinc-50 border border-zinc-200 p-4 text-sm space-y-1">
                        <div><span class="text-zinc-500">Rekening Tujuan:</span> <span class="font-semibold text-zinc-900">BCA 1234-5678-90 a.n. Alaiya Indonesia</span></div>
                        <div><span class="text-zinc-500">Total Transfer:</span> <span class="font-bold text-brand">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                        <div><span class="text-zinc-500">Tenggat Pembayaran:</span> <span class="font-semibold text-amber-700">{{ $order->payment_deadline->format('d M Y, H:i') }} WIB</span></div>
                    </div>

                    @if($order->canUploadProof())
                        <form method="POST" action="{{ route('orders.proof', $order) }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <label class="block text-xs font-semibold text-zinc-700">Unggah Bukti Transfer (.jpg, .png, .pdf, maks 4MB)</label>
                            <input type="file" name="proof" required accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full text-sm text-zinc-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200">
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold transition">
                                Kirim Bukti Pembayaran
                            </button>
                        </form>
                    @endif

                    @if($order->payments->isNotEmpty())
                        <div class="pt-3 border-t border-zinc-200 space-y-2">
                            <div class="text-xs font-semibold text-zinc-700">Riwayat Bukti Transfer</div>
                            @foreach($order->payments as $pay)
                                <div class="text-xs p-3 rounded-lg bg-zinc-50 border border-zinc-200 flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold uppercase">{{ $pay->status }}</span> • Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                        @if($pay->rejection_reason)
                                            <div class="text-rose-600 mt-0.5">Alasan ditolak: {{ $pay->rejection_reason }}</div>
                                        @endif
                                    </div>
                                    <span class="text-zinc-500">{{ $pay->created_at->format('d M, H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Shipping & Tracking Info --}}
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-2 text-sm">
                    <h2 class="font-display font-bold text-lg text-zinc-900">Informasi Pengiriman & Resi</h2>
                    <p class="text-zinc-600">{{ $order->recipient_name }} ({{ $order->recipient_phone }}) — {{ $order->shipping_address }}, {{ $order->postal_code }}</p>
                    <p class="font-medium text-zinc-900">
                        Kurir: {{ $order->courier }} • No. Resi:
                        <span class="text-brand font-bold">{{ $order->tracking_number ?: 'Menunggu pengiriman oleh Admin' }}</span>
                    </p>
                </div>
            </div>

            {{-- Audit Trail --}}
            <div class="lg:col-span-5 bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                <h2 class="font-display font-bold text-lg text-zinc-900">Riwayat Perubahan Status</h2>
                <div class="space-y-3">
                    @foreach($order->histories as $hist)
                        <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-zinc-900">{{ \App\Models\Order::STATUS_LABELS[$hist->to_status] ?? $hist->to_status }}</span>
                                <span class="text-zinc-500">{{ $hist->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-xs text-zinc-600">{{ $hist->note }}</p>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('chat.index') }}" class="block w-full text-center py-2.5 rounded-lg border border-zinc-300 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 transition">
                    Butuh Bantuan? Buka Live Chat Admin
                </a>
            </div>
        </div>
    </section>
@endsection
