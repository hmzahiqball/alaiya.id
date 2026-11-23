@extends('layouts.app')

@section('title', 'Masuk atau Daftar — alaiya.id')

@section('content')
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        {{-- Login Card --}}
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 sm:p-8 space-y-6">
            <div>
                <h1 class="font-display font-bold text-2xl text-zinc-900">Masuk ke Akun alaiya.id</h1>
                <p class="text-sm text-zinc-600 mt-1">
                    Login diperlukan untuk checkout PO, mengunggah bukti pembayaran, melacak pesanan, dan live chat (BR-01).
                </p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login_email" class="block text-sm font-medium text-zinc-800 mb-1">Email</label>
                    <input id="login_email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <div>
                    <label for="login_password" class="block text-sm font-medium text-zinc-800 mb-1">Kata Sandi</label>
                    <input id="login_password" name="password" type="password" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <button type="submit" class="w-full py-3 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                    Masuk
                </button>
            </form>

            {{-- Quick Demo Login for Meeting Presentation --}}
            <div class="pt-4 border-t border-zinc-200 space-y-2.5">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Akses Cepat Prototype (Demo Meeting)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <form method="POST" action="{{ route('login.demo') }}">
                        @csrf
                        <input type="hidden" name="role" value="customer">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-lg border border-zinc-300 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-800 transition">
                            Demo Pelanggan (Siti)
                        </button>
                    </form>
                    <form method="POST" action="{{ route('login.demo') }}">
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-xs font-semibold text-white transition">
                            Demo Admin alaiya.id
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Register Card --}}
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="font-display font-bold text-2xl text-zinc-900">Daftar Akun Pelanggan</h2>
                <p class="text-sm text-zinc-600 mt-1">
                    Pendaftaran publik otomatis membuat akun Pelanggan.
                </p>
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-zinc-800 mb-1">Nama Lengkap</label>
                    <input name="name" type="text" value="{{ old('name') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-800 mb-1">Email</label>
                        <input name="email" type="email" value="{{ old('email') }}" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-800 mb-1">Nomor WhatsApp</label>
                        <input name="phone" type="text" value="{{ old('phone') }}" placeholder="0812..." required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-800 mb-1">Kata Sandi</label>
                        <input name="password" type="password" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-800 mb-1">Ulangi Kata Sandi</label>
                        <input name="password_confirmation" type="password" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-sm transition">
                    Daftar & Mulai Pesan PO
                </button>
            </form>
        </div>
    </section>
@endsection
