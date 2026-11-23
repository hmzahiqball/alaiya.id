<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'alaiya.id — Daily Routine Fashion Pre-Order')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#2D5A4C',
                            dark: '#214439',
                            soft: '#E8F2EE',
                        },
                        sand: '#F4EFEA',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans antialiased min-h-screen flex flex-col">

    {{-- Consistent Single-Row Navbar --}}
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
            <div class="flex items-center gap-10">
                <a href="{{ route('home') }}" class="font-display font-bold text-2xl tracking-tight text-brand">
                    alaiya.id
                </a>
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium">
                    <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'text-brand font-semibold' : 'text-zinc-600 hover:text-zinc-900' }} transition">Katalog PO</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-brand font-semibold' : 'text-zinc-600 hover:text-zinc-900' }} transition">Tentang & Cara PO</a>
                    <a href="{{ route('guide') }}" class="{{ request()->routeIs('guide') ? 'text-brand font-semibold' : 'text-zinc-600 hover:text-zinc-900' }} transition">Panduan Bahan</a>
                    @auth
                        @if(!auth()->user()->isAdmin())
                            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'text-brand font-semibold' : 'text-zinc-600 hover:text-zinc-900' }} transition">Lacak PO</a>
                            <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'text-brand font-semibold' : 'text-zinc-600 hover:text-zinc-900' }} transition">Live Chat</a>
                        @endif
                    @endauth
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @php
                    $cartCount = collect(session('cart.items', []))->sum('quantity');
                @endphp
                <a href="{{ route('checkout.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-sm font-medium text-zinc-800 transition">
                    <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>Keranjang</span>
                    @if($cartCount > 0)
                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded-full bg-brand text-white">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-brand hover:bg-brand-dark text-white text-sm font-semibold transition">
                            Panel Admin
                        </a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-sm font-medium text-zinc-800 transition">
                            {{ auth()->user()->name }}
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-lg text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold transition">
                        Masuk / Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash Notifications --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-5">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Floating Live Chat Shortcut --}}
    <a href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.chat.index') : route('chat.index') }}"
       class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2.5 px-4 py-3 rounded-full bg-brand hover:bg-brand-dark text-white shadow-lg shadow-brand/25 text-sm font-semibold transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <span>Live Chat Admin</span>
    </a>

    {{-- Consistent Footer --}}
    <footer class="bg-white border-t border-zinc-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1 space-y-3">
                <div class="font-display font-bold text-xl text-brand">alaiya.id</div>
                <p class="text-sm text-zinc-600 leading-relaxed">
                    Fashion harian terjangkau untuk mahasiswa dan pegawai. Sistem Pre-Order terpusat tanpa dead stock dan tanpa unsur batik.
                </p>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-zinc-900">Produk PO</h4>
                <ul class="space-y-2 text-sm text-zinc-600">
                    <li><a href="{{ route('catalog.index', ['category' => 'Scarf Harian']) }}" class="hover:text-zinc-900">Scarf Harian</a></li>
                    <li><a href="{{ route('catalog.index', ['category' => 'Daily Wear']) }}" class="hover:text-zinc-900">Daily Wear</a></li>
                    <li><a href="{{ route('catalog.index', ['category' => 'Aksesoris Hijab']) }}" class="hover:text-zinc-900">Aksesoris Hijab</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-zinc-900">Informasi</h4>
                <ul class="space-y-2 text-sm text-zinc-600">
                    <li><a href="{{ route('about') }}" class="hover:text-zinc-900">Tentang & Cara PO</a></li>
                    <li><a href="{{ route('guide') }}" class="hover:text-zinc-900">Panduan Bahan & Ukuran</a></li>
                    <li><a href="{{ route('orders.index') }}" class="hover:text-zinc-900">Lacak Status PO</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-zinc-900">Bantuan & Operasional</h4>
                <ul class="space-y-2 text-sm text-zinc-600">
                    <li><a href="{{ route('chat.index') }}" class="hover:text-zinc-900">Live Chat Website</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-zinc-900">Masuk / Daftar Pelanggan</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="hover:text-zinc-900">Panel Admin</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-zinc-100 py-5 text-center text-xs text-zinc-500">
            © {{ date('Y') }} alaiya.id — Sistem Penjualan Online Pre-Order (Kelompok 8 • 2B-D3).
        </div>
    </footer>
</body>
</html>
