@extends('layouts.app')

@section('title', 'Katalog Pre-Order — alaiya.id')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-brand-soft text-brand text-xs font-semibold mb-2">
                    Sistem Pre-Order Terpusat • Kuota 100 pcs/produk
                </span>
                <h1 class="font-display font-bold text-3xl text-zinc-900">Katalog Pre-Order alaiya.id</h1>
                <p class="text-sm text-zinc-600 mt-1">
                    Fashion harian terjangkau tanpa unsur batik. Produk dengan batch tertutup tetap tampil informatif namun tidak dapat dipesan.
                </p>
            </div>

            {{-- Category Filter --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('catalog.index') }}"
                   class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ $category === '' || $category === 'Semua' ? 'bg-zinc-900 text-white' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('catalog.index', ['category' => $cat]) }}"
                       class="px-3.5 py-2 rounded-lg text-xs font-semibold transition {{ $category === $cat ? 'bg-zinc-900 text-white' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @forelse($products as $product)
                @php
                    $batch = $product->latestBatch;
                    $isOpen = $batch && $batch->isAcceptingOrders();
                    $pct = $batch && $batch->quota_total > 0 ? min(100, round(($batch->allocated_units / $batch->quota_total) * 100)) : 100;
                @endphp
                <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="aspect-[4/3] bg-zinc-100 relative overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold {{ $isOpen ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-200 text-zinc-700' }}">
                                {{ $isOpen ? 'PO Dibuka' : 'Batch Tutup' }}
                            </span>
                        </div>
                        <div class="p-5 space-y-3">
                            <div class="flex items-center justify-between text-xs text-zinc-500">
                                <span>{{ $product->category }}</span>
                                <span>{{ $product->activeVariants->count() }} Varian Warna</span>
                            </div>
                            <h2 class="font-display font-bold text-lg text-zinc-900">{{ $product->name }}</h2>
                            <p class="text-xs text-zinc-600 leading-relaxed">{{ $product->material }} • {{ $product->dimensions }}</p>

                            {{-- Color Swatches Preview --}}
                            <div class="flex items-center gap-1.5 pt-1">
                                @foreach($product->activeVariants as $variant)
                                    <span title="{{ $variant->color_name }}" class="w-4 h-4 rounded-full border border-zinc-300" style="background-color: {{ $variant->color_hex }}"></span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-5 pt-0 space-y-4">
                        @if($batch)
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-xs font-medium">
                                    <span class="text-zinc-600">{{ $batch->batch_code }}</span>
                                    <span class="{{ $isOpen ? 'text-zinc-900' : 'text-zinc-500' }}">
                                        Sisa {{ $batch->remainingQuota() }} / {{ $batch->quota_total }} pcs
                                    </span>
                                </div>
                                <div class="w-full h-2 bg-zinc-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $isOpen ? 'bg-brand' : 'bg-zinc-400' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-1">
                            <span class="font-display font-bold text-xl text-brand">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <a href="{{ route('catalog.show', $product) }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold transition {{ $isOpen ? 'bg-brand hover:bg-brand-dark text-white' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-800' }}">
                                {{ $isOpen ? 'Pilih Varian' : 'Lihat Detail' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-zinc-500 text-sm">
                    Belum ada produk aktif pada kategori ini.
                </div>
            @endforelse
        </div>
    </section>
@endsection
