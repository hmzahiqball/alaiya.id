@extends('layouts.app')

@section('title', 'Profil Saya — alaiya.id')

@section('content')
    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <h1 class="font-display font-bold text-3xl text-zinc-900">Profil Pelanggan</h1>

        <form method="POST" action="{{ route('profile.update') }}" class="bg-white rounded-2xl border border-zinc-200 p-6 sm:p-8 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-zinc-800 mb-1">Nama Lengkap</label>
                <input name="name" type="text" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-800 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-200 bg-zinc-100 text-sm text-zinc-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-800 mb-1">Nomor Telepon / WA</label>
                    <input name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-800 mb-1">Alamat Default</label>
                <textarea name="address" rows="3" class="w-full px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">{{ old('address', $user->address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-800 mb-1">Kode Pos</label>
                <input name="postal_code" type="text" value="{{ old('postal_code', $user->postal_code) }}"
                       class="w-40 px-3.5 py-2.5 rounded-lg border border-zinc-300 bg-zinc-50 text-sm">
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand hover:bg-brand-dark text-white font-semibold text-sm transition">
                Simpan Perubahan
            </button>
        </form>
    </section>
@endsection
