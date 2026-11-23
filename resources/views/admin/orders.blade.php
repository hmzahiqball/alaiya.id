@extends('layouts.admin')

@section('title', 'Antrean PO & Verifikasi — Admin alaiya.id')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl sm:text-3xl text-zinc-900">Antrean PO, Verifikasi & Pemenuhan</h1>
                <p class="text-sm text-zinc-600 mt-1">Periksa bukti transfer, setujui/tolak, dan perbarui status produksi hingga resi pengiriman (FR-05, FR-07).</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-white rounded-xl border border-zinc-200 p-4 flex flex-wrap items-center gap-3">
            <select name="batch_id" class="px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                <option value="">Semua Batch PO</option>
                @foreach($batches as $b)
                    <option value="{{ $b->id }}" {{ $batchFilter === $b->id ? 'selected' : '' }}>{{ $b->batch_code }} — {{ $b->product->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Order::STATUS_LABELS as $k => $lbl)
                    <option value="{{ $k }}" {{ $statusFilter === $k ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-zinc-900 text-white text-sm font-semibold">Filter</button>
        </form>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Queue List --}}
            <div class="lg:col-span-7 bg-white rounded-2xl border border-zinc-200 p-5 space-y-3">
                <h2 class="font-display font-bold text-base text-zinc-900">Daftar Pesanan ({{ $orders->count() }})</h2>
                @forelse($orders as $o)
                    @php $isSelected = $selectedOrder && $selectedOrder->id === $o->id; @endphp
                    <a href="{{ route('admin.orders.index', ['order_id' => $o->id, 'status' => $statusFilter, 'batch_id' => $batchFilter]) }}"
                       class="block p-4 rounded-xl border transition {{ $isSelected ? 'border-brand bg-brand-soft/60' : 'border-zinc-200 bg-zinc-50 hover:bg-zinc-100' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-sm text-zinc-900">{{ $o->order_number }} — {{ $o->user->name }}</div>
                                <div class="text-xs text-zinc-600 mt-0.5">
                                    {{ $o->batch->batch_code }} • {{ $o->totalQuantity() }} pcs • Rp {{ number_format($o->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $isSelected ? 'bg-brand text-white' : 'bg-white border border-zinc-200 text-zinc-800' }}">
                                {{ $o->statusLabel() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-sm text-zinc-500 py-8 text-center">Tidak ada pesanan yang sesuai filter.</div>
                @endforelse
            </div>

            {{-- Right Verification & Fulfillment Panel --}}
            <div class="lg:col-span-5 bg-white rounded-2xl border border-zinc-200 p-6 space-y-5">
                @if($selectedOrder)
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <h2 class="font-display font-bold text-lg text-zinc-900">{{ $selectedOrder->order_number }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full bg-brand-soft text-brand text-xs font-semibold">{{ $selectedOrder->statusLabel() }}</span>
                        </div>
                        <p class="text-xs text-zinc-600">{{ $selectedOrder->recipient_name }} ({{ $selectedOrder->recipient_phone }})</p>
                        <p class="text-xs text-zinc-500">{{ $selectedOrder->shipping_address }}, {{ $selectedOrder->postal_code }}</p>
                    </div>

                    <div class="rounded-xl bg-zinc-50 border border-zinc-200 p-3.5 text-xs space-y-1">
                        @foreach($selectedOrder->items as $it)
                            <div class="flex justify-between">
                                <span>{{ $it->product_name_snapshot }} ({{ $it->variant_name_snapshot }}) × {{ $it->quantity }}</span>
                                <span class="font-semibold">Rp {{ number_format($it->line_total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between text-zinc-500 pt-1 border-t border-zinc-200">
                            <span>Ongkir ({{ $selectedOrder->courier }})</span>
                            <span>Rp {{ number_format($selectedOrder->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-zinc-900">
                            <span>Total Tagihan</span>
                            <span>Rp {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @php $latestPayment = $selectedOrder->payments->first(); @endphp
                    @if($latestPayment && $selectedOrder->status === \App\Models\Order::STATUS_WAITING_VERIFICATION)
                        <div class="space-y-3 pt-2 border-t border-zinc-200">
                            <div class="text-xs font-semibold text-zinc-800">Pemeriksaan Bukti Pembayaran</div>
                            <div class="p-3 rounded-lg bg-zinc-100 text-xs text-zinc-700 flex items-center justify-between">
                                <span>File: {{ basename($latestPayment->proof_path) }}</span>
                                @if(\Illuminate\Support\Facades\Storage::disk('local')->exists($latestPayment->proof_path))
                                    <a href="{{ route('payments.proof', $latestPayment) }}" target="_blank" class="text-brand font-semibold underline">Lihat Bukti</a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.orders.approve', [$selectedOrder, $latestPayment]) }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-xs font-semibold transition">
                                    Setujui Pembayaran → Dibayar
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.orders.reject', [$selectedOrder, $latestPayment]) }}" class="space-y-2">
                                @csrf
                                <input name="reason" type="text" required placeholder="Alasan penolakan bukti..."
                                       class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-xs">
                                <button type="submit" class="w-full py-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition">
                                    Tolak Bukti Pembayaran
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(in_array($selectedOrder->status, [\App\Models\Order::STATUS_PAID, \App\Models\Order::STATUS_IN_PRODUCTION, \App\Models\Order::STATUS_SHIPPED], true))
                        <form method="POST" action="{{ route('admin.orders.advance', $selectedOrder) }}" class="space-y-3 pt-2 border-t border-zinc-200">
                            @csrf
                            <div class="text-xs font-semibold text-zinc-800">Perbarui Status Pemenuhan</div>
                            @php
                                $next = match ($selectedOrder->status) {
                                    \App\Models\Order::STATUS_PAID => \App\Models\Order::STATUS_IN_PRODUCTION,
                                    \App\Models\Order::STATUS_IN_PRODUCTION => \App\Models\Order::STATUS_SHIPPED,
                                    \App\Models\Order::STATUS_SHIPPED => \App\Models\Order::STATUS_COMPLETED,
                                };
                            @endphp
                            <input type="hidden" name="target_status" value="{{ $next }}">
                            @if($next === \App\Models\Order::STATUS_SHIPPED)
                                <input name="courier" type="text" value="{{ $selectedOrder->courier }}" required placeholder="Nama Kurir"
                                       class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-xs">
                                <input name="tracking_number" type="text" required placeholder="Nomor Resi Pengiriman"
                                       class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-xs">
                            @endif
                            <button type="submit" class="w-full py-2.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold transition">
                                Tandai sebagai {{ \App\Models\Order::STATUS_LABELS[$next] }}
                            </button>
                        </form>
                    @endif

                    @if(in_array($selectedOrder->status, [\App\Models\Order::STATUS_WAITING_PAYMENT, \App\Models\Order::STATUS_NEEDS_REVISION], true))
                        <form method="POST" action="{{ route('admin.orders.cancel', $selectedOrder) }}" class="space-y-2 pt-2 border-t border-zinc-200">
                            @csrf
                            <input name="reason" type="text" required placeholder="Alasan pembatalan..."
                                   class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-zinc-50 text-xs">
                            <button type="submit" class="w-full py-2 rounded-lg border border-zinc-300 text-zinc-700 text-xs font-semibold hover:bg-zinc-50">
                                Batalkan Pesanan & Lepas Kuota
                            </button>
                        </form>
                    @endif
                @else
                    <div class="text-sm text-zinc-500 text-center py-8">Pilih pesanan di sebelah kiri untuk memeriksa detail.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
