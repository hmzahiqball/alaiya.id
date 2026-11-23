@extends('layouts.admin')

@section('title', 'Inbox Live Chat — Admin alaiya.id')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-zinc-900">Inbox Live Chat Pelanggan</h1>
            <p class="text-sm text-zinc-600 mt-1">Balas pertanyaan pelanggan langsung dari panel admin (FR-08).</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-4 bg-white rounded-2xl border border-zinc-200 p-4 space-y-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 px-2 pb-1">Percakapan ({{ $conversations->count() }})</div>
                @forelse($conversations as $conv)
                    @php $active = $activeConversation && $activeConversation->id === $conv->id; @endphp
                    <a href="{{ route('admin.chat.index', ['conversation_id' => $conv->id]) }}"
                       class="block p-3 rounded-xl transition {{ $active ? 'bg-brand-soft text-brand' : 'hover:bg-zinc-50 text-zinc-800' }}">
                        <div class="font-semibold text-sm">{{ $conv->user->name }}</div>
                        <div class="text-xs text-zinc-500 truncate mt-0.5">{{ $conv->messages->last()?->body ?? 'Belum ada pesan' }}</div>
                    </a>
                @empty
                    <div class="text-xs text-zinc-500 p-4 text-center">Belum ada percakapan.</div>
                @endforelse
            </div>

            <div class="lg:col-span-8 bg-white rounded-2xl border border-zinc-200 overflow-hidden">
                @if($activeConversation)
                    <div class="px-6 py-4 border-b border-zinc-200 font-semibold text-zinc-900">
                        Percakapan dengan {{ $activeConversation->user->name }} ({{ $activeConversation->user->email }})
                    </div>
                    <div class="p-6 bg-zinc-50 space-y-3 min-h-[320px] max-h-[420px] overflow-y-auto">
                        @foreach($activeConversation->messages as $msg)
                            @php $isAdmin = $msg->sender->isAdmin(); @endphp
                            <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-md rounded-2xl px-4 py-2.5 text-sm {{ $isAdmin ? 'bg-brand text-white' : 'bg-white border border-zinc-200 text-zinc-900' }}">
                                    <div class="text-[11px] font-semibold mb-0.5 {{ $isAdmin ? 'text-emerald-100' : 'text-brand' }}">
                                        {{ $isAdmin ? 'Admin' : $activeConversation->user->name }} • {{ $msg->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB
                                    </div>
                                    <p>{{ $msg->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('admin.chat.send', $activeConversation) }}" class="p-4 border-t border-zinc-200 flex gap-3">
                        @csrf
                        <input name="body" type="text" required placeholder="Tulis balasan untuk pelanggan..."
                               class="flex-1 px-4 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold">
                            Kirim Balasan
                        </button>
                    </form>
                @else
                    <div class="p-10 text-center text-sm text-zinc-500">Pilih percakapan untuk membalas pesan pelanggan.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
