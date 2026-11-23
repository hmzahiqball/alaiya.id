@extends('layouts.app')

@section('title', 'Keranjang & Checkout PO — alaiya.id')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-display font-bold text-3xl text-zinc-900">Keranjang & Checkout Pre-Order</h1>
                <p class="text-sm text-zinc-600 mt-1">Satu pesanan memuat varian dari satu batch PO (BR-06).</p>
            </div>
            @if($batch)
                <span class="px-3.5 py-1.5 rounded-full bg-brand-soft text-brand text-xs font-semibold">
                    {{ $batch->title }} • Sisa Kuota: {{ $batch->remainingQuota() }} pcs
                </span>
            @endif
        </div>

        @if(empty($cart['items']))
            <div class="bg-white rounded-2xl border border-zinc-200 p-12 text-center space-y-4">
                <div class="text-zinc-600 text-sm">Keranjang PO Anda masih kosong.</div>
                <a href="{{ route('catalog.index') }}" class="inline-block px-6 py-3 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                    Pilih Produk di Katalog PO
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                @csrf

                <div class="lg:col-span-7 space-y-6">
                    {{-- Items Card --}}
                    <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                        <h2 class="font-display font-bold text-lg text-zinc-900">Item Pre-Order</h2>
                        @foreach($cart['items'] as $item)
                            <div class="flex items-center justify-between gap-4 p-4 rounded-xl bg-zinc-50 border border-zinc-200">
                                <div class="flex items-center gap-3.5">
                                    <span class="w-8 h-8 rounded-lg border border-zinc-300 flex-shrink-0" style="background-color: {{ $item['color_hex'] }}"></span>
                                    <div>
                                        <div class="font-semibold text-sm text-zinc-900">{{ $item['product_name'] }}</div>
                                        <div class="text-xs text-zinc-600">Varian: {{ $item['variant_name'] }} • {{ $item['quantity'] }} pcs × Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="font-semibold text-sm text-zinc-900">
                                        Rp {{ number_format($item['unit_price'] * $item['quantity'], 0, ',', '.') }}
                                    </div>
                                    <a href="{{ route('checkout.remove', $item['variant_id']) }}" class="text-xs text-rose-600 hover:underline">Hapus</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Shipping Card --}}
                    <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                        <h2 class="font-display font-bold text-lg text-zinc-900">Data Penerima & Pengiriman</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Penerima</label>
                                <input name="recipient_name" type="text" value="{{ old('recipient_name', $user->name) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor Telepon / WA</label>
                                <input name="recipient_phone" type="text" value="{{ old('recipient_phone', $user->phone) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1">Alamat Lengkap</label>
                            <textarea name="shipping_address" rows="3" required class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">{{ old('shipping_address', $user->address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1">Kode Pos</label>
                                <input name="postal_code" type="text" value="{{ old('postal_code', $user->postal_code) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1">Kurir (Ongkir Tetap D-06)</label>
                                <select name="courier" class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                                    <option value="JNE Reguler">JNE Reguler — Rp 12.000</option>
                                    <option value="J&T Express">J&T Express — Rp 12.000</option>
                                    <option value="SiCepat REG">SiCepat REG — Rp 12.000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Card --}}
                <div class="lg:col-span-5 bg-white rounded-2xl border border-zinc-200 p-6 space-y-5 sticky top-28">
                    <h2 class="font-display font-bold text-xl text-zinc-900">Ringkasan Tagihan PO</h2>
                    <div class="space-y-2.5 text-sm border-b border-zinc-200 pb-4">
                        <div class="flex justify-between text-zinc-600">
                            <span>Subtotal Barang</span>
                            <span class="font-medium text-zinc-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-600">
                            <span>Ongkos Kirim (Flat)</span>
                            <span class="font-medium text-zinc-900">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-zinc-900 pt-2">
                            <span>Total Pembayaran</span>
                            <span class="text-brand text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3.5 text-xs text-amber-900 space-y-1">
                        <div class="font-semibold">Tenggat Pembayaran 24 Jam (BR-04)</div>
                        <p>Konfirmasi PO akan langsung mencadangkan kuota batch. Pembayaran menggunakan Transfer Bank Manual (eksplorasi Midtrans pada fase lanjutan).</p>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                        Konfirmasi Pesanan PO
                    </button>
                </div>
            </form>
        @endif
    </section>
@endsection
