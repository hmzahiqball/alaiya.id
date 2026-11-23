@extends('layouts.app')

@section('title', 'Panduan Bahan & Ukuran — alaiya.id')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <div class="space-y-3">
            <span class="px-3 py-1 rounded-full bg-brand-soft text-brand text-xs font-semibold">Panduan Produk</span>
            <h1 class="font-display font-bold text-3xl sm:text-4xl text-zinc-900">Panduan Bahan, Ukuran & Alur Pre-Order</h1>
            <p class="text-base text-zinc-600">Detail spesifikasi material dan dimensi yang dipublikasikan pada setiap item alaiya.id.</p>
        </div>

        <div class="space-y-4">
            @foreach([
                ['Daily Voal Scarf (115 × 115 cm)', 'Voal Ultrafine Premium — ringan, tegak di dahi, tidak menerawang, dan adem dipakai kuliah atau kerja seharian.'],
                ['Pashmina Ceruty Daily (180 × 75 cm)', 'Ceruty Babydoll Armany — tekstur pasir lembut, jatuh rapi, mudah dikreasikan tanpa jarum berlebihan.'],
                ['Blouse Linen Rutin (All Size Fit S–XL)', 'Cotton Linen Blend — lingkar dada 104 cm, panjang 65 cm, nyaman untuk aktivitas kantor maupun kampus.'],
            ] as [$title, $desc])
                <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-1.5">
                    <h2 class="font-display font-bold text-lg text-zinc-900">{{ $title }}</h2>
                    <p class="text-sm text-zinc-600 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
