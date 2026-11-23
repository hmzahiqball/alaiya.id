@extends('layouts.admin')

@section('title', 'Master Produk & Batch PO — Admin alaiya.id')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-zinc-900">Master Item Produk & Batch PO</h1>
            <p class="text-sm text-zinc-600 mt-1">Kelola katalog produk harian non-batik, detail dimensi ukuran, varian warna, serta kuota batch PO (FR-03).</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
            {{-- Form Tambah Master Produk --}}
            <form method="POST" action="{{ route('admin.products.store') }}" class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                @csrf
                <h2 class="font-display font-bold text-lg text-zinc-900">1. Tambah Master Item Baru</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Produk</label>
                        <input name="name" type="text" required placeholder="Contoh: Alaiya Daily Voal Scarf"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Kategori (Non-Batik)</label>
                        <input name="category" type="text" required value="Scarf Harian"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Harga Satuan (Rp)</label>
                        <input name="price" type="number" min="1000" required value="49000"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Bahan</label>
                        <input name="material" type="text" required placeholder="Voal Ultrafine Premium"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Dimensi / Panduan Ukuran (Dipublikasikan Detail)</label>
                    <input name="dimensions" type="text" required placeholder="115 cm × 115 cm (All Size / Standar Harian)"
                           class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Varian Warna (pisahkan dengan koma)</label>
                    <input name="colors" type="text" required value="Sage Green, Dusty Rose, Cream Oat"
                           class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Deskripsi Produk</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm"></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold transition">
                    Simpan & Terbitkan Produk
                </button>
            </form>

            {{-- Form Buka Batch PO Baru --}}
            <form method="POST" action="{{ route('admin.batches.store') }}" class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
                @csrf
                <h2 class="font-display font-bold text-lg text-zinc-900">2. Buka Batch PO Baru (Maks. 1 Aktif / Produk)</h2>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Pilih Produk</label>
                    <select name="product_id" required class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Kode Batch</label>
                        <input name="batch_code" type="text" required placeholder="PO-13-VOAL"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Judul Batch</label>
                        <input name="title" type="text" required placeholder="Batch PO #13"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Periode Mulai</label>
                        <input name="start_at" type="datetime-local" required value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Periode Selesai</label>
                        <input name="end_at" type="datetime-local" required value="{{ now()->addDays(14)->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Kuota Total (pcs)</label>
                        <input name="quota_total" type="number" min="1" required value="100"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Estimasi Produksi</label>
                        <input name="production_estimate" type="text" required value="7-10 hari kerja"
                               class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    </div>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold transition">
                    Buka Batch PO
                </button>
            </form>
        </div>

        {{-- Daftar Produk & Batch --}}
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
            <h2 class="font-display font-bold text-lg text-zinc-900">Daftar Produk & Pengaturan Batch PO</h2>
            <div class="space-y-4">
                @foreach($products as $product)
                    <div class="rounded-xl border border-zinc-200 p-4 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-base text-zinc-900">{{ $product->name }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-200 text-zinc-700' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <div class="text-xs text-zinc-600 mt-0.5">
                                    Rp {{ number_format($product->price, 0, ',', '.') }} • {{ $product->material }} • {{ $product->dimensions }}
                                </div>
                                <div class="text-xs text-zinc-500 mt-0.5">
                                    Varian: {{ $product->variants->pluck('color_name')->join(', ') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3.5 py-2 rounded-lg border border-zinc-300 text-xs font-semibold text-zinc-800 hover:bg-zinc-50">
                                    {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>

                        @if($product->batches->isNotEmpty())
                            <div class="space-y-2 pt-2 border-t border-zinc-100">
                                @foreach($product->batches as $batch)
                                    <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="flex flex-wrap items-center justify-between gap-3 bg-zinc-50 p-3 rounded-lg text-xs">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <span class="font-semibold text-zinc-900">{{ $batch->batch_code }}</span> — {{ $batch->title }}
                                            <span class="text-zinc-500 ml-2">Teralokasi: {{ $batch->allocated_units }} / {{ $batch->quota_total }} pcs</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="quota_total" value="{{ $batch->quota_total }}" min="1" class="w-20 px-2 py-1 rounded border border-zinc-300 bg-white">
                                            <select name="status" class="px-2 py-1 rounded border border-zinc-300 bg-white">
                                                <option value="scheduled" {{ $batch->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                                <option value="open" {{ $batch->status === 'open' ? 'selected' : '' }}>Terbuka</option>
                                                <option value="closed" {{ $batch->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                                            </select>
                                            <button type="submit" class="px-3 py-1 rounded bg-brand text-white font-semibold">Simpan</button>
                                        </div>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
