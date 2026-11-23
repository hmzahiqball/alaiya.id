@extends('layouts.app')

@section('title', 'Live Chat Bantuan — alaiya.id')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden shadow-sm">
            <div class="bg-brand px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="font-display font-bold text-lg text-white">Live Chat Bantuan alaiya.id</h1>
                    <p class="text-xs text-emerald-100 mt-0.5">Terhubung langsung dengan Admin • Riwayat pesan tersimpan otomatis (FR-08)</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-semibold">Admin Online</span>
            </div>

            <div class="p-6 bg-zinc-50 space-y-4 min-h-[360px] max-h-[480px] overflow-y-auto">
                @forelse($conversation->messages as $msg)
                    @php $isMe = $msg->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md rounded-2xl px-4 py-3 text-sm {{ $isMe ? 'bg-brand text-white' : 'bg-white border border-zinc-200 text-zinc-900' }}">
                            <div class="text-[11px] font-semibold mb-1 {{ $isMe ? 'text-emerald-100' : 'text-brand' }}">
                                {{ $isMe ? 'Anda' : 'Admin alaiya.id' }} • {{ $msg->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB
                            </div>
                            <p class="leading-relaxed">{{ $msg->body }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-sm text-zinc-500 py-12">
                        Belum ada pesan. Silakan kirim pertanyaan mengenai bahan, ukuran, atau pesanan PO Anda.
                    </div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('chat.send') }}" class="p-4 bg-white border-t border-zinc-200 flex gap-3">
                @csrf
                <input name="body" type="text" required placeholder="Tulis pesan untuk Admin..."
                       class="flex-1 px-4 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold transition">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </section>
@endsection
