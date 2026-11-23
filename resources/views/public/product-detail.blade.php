@extends('layouts.app')

@section('title', $product->name . ' — alaiya.id')

@section('content')
    @php
        $batch = $product->latestBatch;
        $isOpen = $batch && $batch->isAcceptingOrders();
        $remaining = $batch ? $batch->remainingQuota() : 0;
    @endphp

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-14">
        <div class="text-xs font-medium text-zinc-500 flex items-center gap-2">
            <a href="{{ route('catalog.index') }}" class="hover:text-zinc-900">Katalog PO</a>
            <span>/</span>
            <span>{{ $product->category }}</span>
            <span>/</span>
            <span class="text-zinc-900">{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Left: Product Photo --}}
            <div class="lg:col-span-6 space-y-4">
                <div class="aspect-square rounded-2xl overflow-hidden bg-zinc-100 border border-zinc-200">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Right: Detail, Specs, Variant & Quantity --}}
            <div class="lg:col-span-6 space-y-6">
                <div class="flex flex-wrap items-center gap-2">
                    @if($batch)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isOpen ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-200 text-zinc-700' }}">
                            {{ $batch->title }} ({{ $isOpen ? 'Dibuka' : 'Ditutup' }})
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900">
                            Sisa Kuota: {{ $remaining }} / {{ $batch->quota_total }} pcs
                        </span>
                    @endif
                </div>

                <div>
                    <h1 class="font-display font-bold text-3xl sm:text-4xl text-zinc-900">{{ $product->name }}</h1>
                    <div class="font-display font-bold text-2xl text-brand mt-2">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                </div>

                <p class="text-sm sm:text-base text-zinc-600 leading-relaxed">
                    {{ $product->description }}
                </p>

                {{-- Detailed Specs Box (Notulensi 1: ukuran umum dipublikasikan detail) --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 space-y-2.5 text-sm">
                    <div class="font-semibold text-zinc-900">Spesifikasi Produk & Informasi Pre-Order</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-zinc-600">
                        <div><span class="font-medium text-zinc-800">Bahan:</span> {{ $product->material }}</div>
                        <div><span class="font-medium text-zinc-800">Dimensi / Ukuran:</span> {{ $product->dimensions }}</div>
                        @if($batch)
                            <div><span class="font-medium text-zinc-800">Periode PO:</span> {{ $batch->start_at->format('d M') }} – {{ $batch->end_at->format('d M Y') }}</div>
                            <div><span class="font-medium text-zinc-800">Estimasi Proses:</span> {{ $batch->production_estimate }}</div>
                        @endif
                    </div>
                </div>

                @if($isOpen)
                    <form method="POST" action="{{ route('checkout.add') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Variant Selector --}}
                        <div class="space-y-2.5">
                            <label class="block text-sm font-semibold text-zinc-900">Pilih Varian Warna</label>
                            <div class="flex flex-wrap gap-2.5">
                                @foreach($product->activeVariants as $idx => $variant)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="variant_id" value="{{ $variant->id }}" class="peer sr-only" {{ $idx === 0 ? 'checked' : '' }}>
                                        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-zinc-200 bg-white text-sm font-medium text-zinc-800 peer-checked:border-brand peer-checked:bg-brand-soft peer-checked:text-brand peer-checked:font-semibold transition">
                                            <span class="w-3.5 h-3.5 rounded-full border border-zinc-300" style="background-color: {{ $variant->color_hex }}"></span>
                                            {{ $variant->color_name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Quantity --}}
                        <div class="space-y-2">
                            <label for="quantity" class="block text-sm font-semibold text-zinc-900">Jumlah Pesanan (pcs)</label>
                            <div class="flex items-center gap-3">
                                <input id="quantity" name="quantity" type="number" min="1" max="{{ $remaining }}" value="1"
                                       class="w-28 px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-white text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand">
                                <span class="text-xs text-zinc-500">Maksimal sesuai sisa kuota batch ({{ $remaining }} pcs)</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" class="flex-1 px-6 py-3.5 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                                Tambah ke Keranjang & Checkout PO
                            </button>
                            <a href="{{ route('chat.index') }}" class="px-5 py-3.5 rounded-lg border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-900 font-semibold text-sm text-center transition">
                                Tanya Admin (Live Chat)
                            </a>
                        </div>
                    </form>
                @else
                    <div class="rounded-xl border border-zinc-200 bg-zinc-100 p-4 text-sm text-zinc-700">
                        Batch Pre-Order untuk produk ini sedang ditutup atau kuota sudah terpenuhi (AC-02). Silakan pilih produk lain atau hubungi admin melalui Live Chat.
                    </div>
                @endif
            </div>
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <div class="pt-8 border-t border-zinc-200 space-y-6">
                <h2 class="font-display font-bold text-2xl text-zinc-900">Produk Pre-Order Lainnya</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($relatedProducts as $rel)
                        <a href="{{ route('catalog.show', $rel) }}" class="bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-md transition">
                            <div class="aspect-[4/3] bg-zinc-100">
                                <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-4 space-y-1">
                                <h3 class="font-display font-semibold text-base text-zinc-900">{{ $rel->name }}</h3>
                                <p class="text-xs text-zinc-500">{{ $rel->dimensions }}</p>
                                <div class="font-semibold text-brand pt-1">Rp {{ number_format($rel->price, 0, ',', '.') }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
