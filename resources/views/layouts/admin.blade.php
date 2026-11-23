<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — alaiya.id')</title>
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
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans antialiased min-h-screen flex flex-col lg:flex-row">

    {{-- Admin Sidebar --}}
    <aside class="w-full lg:w-64 bg-white border-b lg:border-b-0 lg:border-r border-zinc-200 flex-shrink-0 flex flex-col justify-between p-6">
        <div class="space-y-6">
            <div>
                <a href="{{ route('admin.products.index') }}" class="font-display font-bold text-xl text-brand">alaiya.id Admin</a>
                <p class="text-xs text-zinc-500 mt-1">Operasional Tunggal (BR-01)</p>
            </div>

            <nav class="space-y-1.5 text-sm font-medium">
                <a href="{{ route('admin.products.index') }}"
                   class="block px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.batches.*') ? 'bg-brand-soft text-brand font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}">
                    Master Produk & Batch
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="block px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('admin.orders.*') ? 'bg-brand-soft text-brand font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}">
                    Antrean PO & Verifikasi
                </a>
                <a href="{{ route('admin.chat.index') }}"
                   class="block px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('admin.chat.*') ? 'bg-brand-soft text-brand font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}">
                    Inbox Live Chat
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="block px-3.5 py-2.5 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-soft text-brand font-semibold' : 'text-zinc-700 hover:bg-zinc-100' }}">
                    Laporan & Rekap Kuota
                </a>
            </nav>
        </div>

        <div class="pt-6 border-t border-zinc-200 space-y-2 mt-6">
            <a href="{{ route('catalog.index') }}" class="block w-full text-center px-3.5 py-2 rounded-lg border border-zinc-200 text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition">
                Lihat Website Publik
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full px-3.5 py-2 rounded-lg text-sm font-medium text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition">
                    Keluar ({{ auth()->user()->name }})
                </button>
            </form>
        </div>
    </aside>

    {{-- Admin Main --}}
    <main class="flex-1 p-6 lg:p-10 max-w-6xl w-full">
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
