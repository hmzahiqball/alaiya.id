@extends('layouts.app')

@section('title', 'Tentang alaiya.id & Cara Pre-Order')

@section('content')
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <div class="space-y-3">
            <span class="px-3 py-1 rounded-full bg-brand-soft text-brand text-xs font-semibold">Tentang Brand</span>
            <h1 class="font-display font-bold text-3xl sm:text-4xl text-zinc-900">Tentang alaiya.id & Sistem Pre-Order</h1>
            <p class="text-base text-zinc-600 leading-relaxed">
                alaiya.id hadir menyediakan fashion harian yang terjangkau, nyaman, dan modern tanpa unsur batik untuk mahasiswa serta pegawai.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200 p-6 sm:p-8 space-y-5 text-sm sm:text-base text-zinc-700 leading-relaxed">
            <h2 class="font-display font-bold text-xl text-zinc-900">Mengapa Sistem Pre-Order (PO) Terpusat?</h2>
            <p>
                Belajar dari pengalaman sebelumnya yang masih tersebar, website alaiya.id menyatukan katalog, pemesanan batch PO (kuota 100 pcs per produk), verifikasi pembayaran, pelacakan status produksi hingga resi, dan Live Chat pelanggan-admin di satu tempat agar tidak terjadi dead stock maupun kesalahan pencatatan.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <div class="font-semibold text-zinc-900">1. Pilih Varian & Checkout</div>
                    <p class="text-xs text-zinc-600">Kuota batch langsung dicadangkan saat Anda mengonfirmasi pesanan PO.</p>
                </div>
                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <div class="font-semibold text-zinc-900">2. Upload Bukti (Maks. 24 Jam)</div>
                    <p class="text-xs text-zinc-600">Unggah bukti transfer sebelum tenggat agar pesanan masuk daftar verifikasi.</p>
                </div>
                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <div class="font-semibold text-zinc-900">3. Produksi & Pengiriman</div>
                    <p class="text-xs text-zinc-600">Setelah batch tutup, pesanan dibayar masuk produksi dan nomor resi diinput admin.</p>
                </div>
                <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <div class="font-semibold text-zinc-900">4. Live Chat Terintegrasi</div>
                    <p class="text-xs text-zinc-600">Bertanya langsung kepada admin di website dengan riwayat pesan tersimpan.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
