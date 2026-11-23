@extends('layouts.app')

@section('title', 'alaiya.id — Daily Routine Fashion Pre-Order')

@section('content')
    {{-- Hero Section (Refined from Figma Landing Page) --}}
    <section class="bg-sand border-b border-zinc-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-soft text-brand text-xs font-semibold">
                    Batch PO #12 Sedang Dibuka • Kuota Terbatas 100 pcs/produk
                </span>
                <h1 class="font-display font-bold text-3xl sm:text-5xl text-zinc-900 leading-tight tracking-tight">
                    Fashion Harian Nyaman & Terjangkau untuk Rutinitasmu
                </h1>
                <p class="text-base sm:text-lg text-zinc-600 leading-relaxed max-w-2xl">
                    Scarf dan daily wear polos modern tanpa unsur batik, dirancang khusus untuk mahasiswa dan pegawai. Pemesanan menggunakan sistem Pre-Order (PO) terpusat agar stok selalu terkontrol tanpa dead stock.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="{{ route('catalog.index') }}" class="px-6 py-3 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                        Lihat Katalog PO #12
                    </a>
                    <a href="{{ route('about') }}" class="px-6 py-3 rounded-lg bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-900 font-semibold text-sm transition">
                        Cara Kerja Pre-Order
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                @php $heroProduct = $featuredProducts->first(); @endphp
                @if($heroProduct)
                    <div class="bg-white rounded-2xl border border-zinc-200 p-5 shadow-sm space-y-4">
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-zinc-100">
                            <img src="{{ $heroProduct->image_url }}" alt="{{ $heroProduct->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="text-xs font-semibold text-brand uppercase tracking-wide">Best Seller Batch #12</span>
                                <h2 class="font-display font-bold text-lg text-zinc-900 mt-0.5">{{ $heroProduct->name }}</h2>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $heroProduct->dimensions }} • {{ $heroProduct->material }}</p>
                            </div>
                            <div class="text-right">
                                <div class="font-display font-bold text-lg text-brand">Rp {{ number_format($heroProduct->price, 0, ',', '.') }}</div>
                                @if($heroProduct->latestBatch)
                                    <div class="text-xs text-zinc-500">Sisa {{ $heroProduct->latestBatch->remainingQuota() }}/{{ $heroProduct->latestBatch->quota_total }}</div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('catalog.show', $heroProduct) }}" class="block w-full text-center py-2.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold transition">
                            Pesan Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Featured Catalog Grid --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h2 class="font-display font-bold text-2xl sm:text-3xl text-zinc-900">Koleksi Daily Routine alaiya.id</h2>
                <p class="text-sm text-zinc-600 mt-1">Pilih produk untuk melihat detail bahan, ukuran lengkap, varian warna, dan sisa kuota PO.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-brand hover:underline">
                Lihat Semua Katalog →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @php
                    $batch = $product->latestBatch;
                    $isOpen = $batch && $batch->isAcceptingOrders();
                @endphp
                <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="aspect-[4/3] bg-zinc-100 relative overflow-hidden">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-xs font-semibold {{ $isOpen ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-200 text-zinc-700' }}">
                                {{ $isOpen ? 'PO Dibuka' : 'Batch Tutup' }}
                            </span>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="text-xs text-zinc-500">{{ $product->category }}</div>
                            <h3 class="font-display font-bold text-base text-zinc-900">{{ $product->name }}</h3>
                            <p class="text-xs text-zinc-600 line-clamp-2">{{ $product->dimensions }}</p>
                        </div>
                    </div>
                    <div class="p-4 pt-0 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-display font-bold text-lg text-brand">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @if($batch)
                                <span class="text-xs font-medium text-zinc-600">Sisa {{ $batch->remainingQuota() }}/{{ $batch->quota_total }}</span>
                            @endif
                        </div>
                        <a href="{{ route('catalog.show', $product) }}" class="block w-full text-center py-2.5 rounded-lg text-sm font-semibold transition {{ $isOpen ? 'bg-brand hover:bg-brand-dark text-white' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-800' }}">
                            {{ $isOpen ? 'Lihat Detail & Pesan' : 'Lihat Detail (Tutup)' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Customer Testimonials (Refined from Figma Landing Page) --}}
    <section class="bg-white border-y border-zinc-200 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div>
                <h2 class="font-display font-bold text-2xl text-zinc-900">Dipercaya Mahasiswa & Pegawai</h2>
                <p class="text-sm text-zinc-600 mt-1">Alur pemesanan terpusat yang jelas dari checkout hingga barang tiba.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['“Bahannya adem banget buat kuliah seharian, harganya juga ramah di kantong mahasiswa!”', 'Siti Aisyah', 'Mahasiswa • Bandung'],
                    ['“Sistem PO-nya jelas, status pembayaran, produksi, hingga resi bisa langsung dilacak di website.”', 'Rina Putri', 'Pegawai Swasta • Bandung'],
                    ['“Admin live chat responsif, ukuran scarf dijelaskan detail sebelum pesan jadi nggak ragu.”', 'Dewi Lestari', 'Pelanggan Tetap alaiya.id'],
                ] as [$quote, $name, $role])
                    <div class="rounded-2xl border border-zinc-200 p-6 bg-zinc-50/70 flex flex-col justify-between gap-6">
                        <p class="text-sm text-zinc-800 leading-relaxed font-medium">{{ $quote }}</p>
                        <div>
                            <div class="text-sm font-semibold text-zinc-900">{{ $name }}</div>
                            <div class="text-xs text-zinc-500">{{ $role }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
