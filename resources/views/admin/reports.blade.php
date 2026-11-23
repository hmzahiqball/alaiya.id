@extends('layouts.admin')

@section('title', 'Laporan & Rekap Kuota — Admin alaiya.id')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-zinc-900">Laporan Penjualan & Rekap Kuota</h1>
                <p class="text-sm text-zinc-600 mt-1">RPT-01 (Penjualan Aktual Dibayar) & RPT-02 (Rekap Kuota & Produksi per Varian) • Zona Waktu WIB</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.export-sales', request()->query()) }}" class="px-4 py-2 rounded-lg bg-brand hover:bg-brand-dark text-white text-xs font-semibold">
                    Ekspor Penjualan (CSV)
                </a>
                <a href="{{ route('admin.reports.export-quota', request()->query()) }}" class="px-4 py-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold">
                    Ekspor Rekap Produksi (CSV)
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.reports.index') }}" class="bg-white rounded-xl border border-zinc-200 p-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1">Batch PO</label>
                <select name="batch_id" class="px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                    <option value="">Semua Batch</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}" {{ $filters['batchId'] === $b->id ? 'selected' : '' }}>{{ $b->batch_code }} — {{ $b->product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1">Verifikasi Dari (WIB)</label>
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}" class="px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-zinc-600 mb-1">Sampai (WIB)</label>
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}" class="px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-zinc-900 text-white text-sm font-semibold">Terapkan Filter</button>
        </form>

        {{-- KPI Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <div class="text-xs text-zinc-500">Pesanan Dibayar (RPT-01)</div>
                <div class="font-display font-bold text-2xl text-zinc-900 mt-1">{{ $salesOrders->count() }} Pesanan</div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <div class="text-xs text-zinc-500">Total Penjualan (Subtotal Barang, BR-08)</div>
                <div class="font-display font-bold text-2xl text-brand mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <div class="text-xs text-zinc-500">Total Ongkir (Terpisah)</div>
                <div class="font-display font-bold text-2xl text-zinc-900 mt-1">Rp {{ number_format($totalShipping, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- RPT-02 Table --}}
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 space-y-4">
            <h2 class="font-display font-bold text-lg text-zinc-900">RPT-02: Rekap Kuota & Unit Dibayar per Varian (Untuk Produksi)</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-zinc-500 border-b border-zinc-200">
                        <tr>
                            <th class="py-2.5 pr-4">Produk</th>
                            <th class="py-2.5 pr-4">Varian Warna</th>
                            <th class="py-2.5 pr-4">Unit Dibayar (Produksi)</th>
                            <th class="py-2.5">Unit Dicadangkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($variantSummary as $row)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-zinc-900">{{ $row['product'] }}</td>
                                <td class="py-3 pr-4 text-zinc-700">{{ $row['variant'] }}</td>
                                <td class="py-3 pr-4 font-semibold text-brand">{{ $row['paid_units'] }} pcs</td>
                                <td class="py-3 text-zinc-600">{{ $row['reserved_units'] }} pcs</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-zinc-500">Belum ada data transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
