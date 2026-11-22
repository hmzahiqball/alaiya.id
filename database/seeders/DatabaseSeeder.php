<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PoBatch;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin alaiya.id',
            'email' => 'admin@alaiya.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0811-2233-4455',
            'address' => 'Studio alaiya.id, Bandung',
            'postal_code' => '40153',
        ]);

        $siti = User::create([
            'name' => 'Siti Aisyah',
            'email' => 'siti@mahasiswa.ac.id',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Gegerkalong Hilir No. 45, Kec. Sukasari, Kota Bandung',
            'postal_code' => '40153',
        ]);

        $rina = User::create([
            'name' => 'Rina Putri',
            'email' => 'rina@pegawai.id',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '0813-9876-5432',
            'address' => 'Jl. Setiabudi No. 112, Kota Bandung',
            'postal_code' => '40141',
        ]);

        // Produk 1: Alaiya Daily Voal Scarf
        $p1 = Product::create([
            'name' => 'Alaiya Daily Voal Scarf',
            'slug' => 'alaiya-daily-voal-scarf',
            'category' => 'Scarf Harian',
            'price' => 49000,
            'material' => 'Voal Ultrafine Premium',
            'dimensions' => '115 cm × 115 cm (All Size / Standar Harian)',
            'description' => 'Scarf harian berbahan Voal Ultrafine yang ringan, tidak menerawang, dan mudah dibentuk untuk aktivitas kuliah maupun kerja seharian. Desain polos modern tanpa unsur batik.',
            'image_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80',
            'is_active' => true,
        ]);
        $p1Variants = [
            ['Sage Green', '#6F8F7E'],
            ['Dusty Rose', '#C48B8E'],
            ['Cream Oat', '#E0D5C4'],
            ['Charcoal', '#36393F'],
            ['Navy', '#253659'],
        ];
        $v1Models = [];
        foreach ($p1Variants as [$cName, $cHex]) {
            $v1Models[] = ProductVariant::create([
                'product_id' => $p1->id,
                'color_name' => $cName,
                'color_hex' => $cHex,
                'is_active' => true,
            ]);
        }
        $b1 = PoBatch::create([
            'product_id' => $p1->id,
            'batch_code' => 'PO-12-VOAL',
            'title' => 'Batch PO #12 — Daily Voal',
            'start_at' => now()->subDays(3),
            'end_at' => now()->addDays(11),
            'quota_total' => 100,
            'allocated_units' => 35,
            'status' => 'open',
            'production_estimate' => '7-10 hari kerja setelah batch tutup',
        ]);

        // Produk 2: Alaiya Pashmina Ceruty Daily
        $p2 = Product::create([
            'name' => 'Alaiya Pashmina Ceruty Daily',
            'slug' => 'alaiya-pashmina-ceruty-daily',
            'category' => 'Scarf Harian',
            'price' => 55000,
            'material' => 'Ceruty Babydoll Armany',
            'dimensions' => '180 cm × 75 cm (Jatuh & Mudah Diatur)',
            'description' => 'Pashmina harian berbahan Ceruty Babydoll yang flowy, ringan, dan nyaman dipakai dari pagi hingga sore. Polos minimalis tanpa motif batik.',
            'image_url' => 'https://images.unsplash.com/photo-1601924994987-69e26d50dc26?auto=format&fit=crop&w=900&q=80',
            'is_active' => true,
        ]);
        $v2Models = [];
        foreach ([['Warm Oat', '#D9CBB8'], ['Mocha', '#8C6D5A'], ['Black', '#18181B'], ['Soft Olive', '#7A8463']] as [$cName, $cHex]) {
            $v2Models[] = ProductVariant::create([
                'product_id' => $p2->id,
                'color_name' => $cName,
                'color_hex' => $cHex,
                'is_active' => true,
            ]);
        }
        $b2 = PoBatch::create([
            'product_id' => $p2->id,
            'batch_code' => 'PO-12-PASH',
            'title' => 'Batch PO #12 — Pashmina Ceruty',
            'start_at' => now()->subDays(3),
            'end_at' => now()->addDays(11),
            'quota_total' => 100,
            'allocated_units' => 58,
            'status' => 'open',
            'production_estimate' => '7-10 hari kerja setelah batch tutup',
        ]);

        // Produk 3: Alaiya Blouse Linen Rutin
        $p3 = Product::create([
            'name' => 'Alaiya Blouse Linen Rutin',
            'slug' => 'alaiya-blouse-linen-rutin',
            'category' => 'Daily Wear',
            'price' => 89000,
            'material' => 'Cotton Linen Blend Breathable',
            'dimensions' => 'All Size Fit S–XL (LD 104 cm, Panjang 65 cm, Lengan 56 cm)',
            'description' => 'Atasan harian berbahan linen blend yang rapi untuk ke kantor maupun kampus. Potongan relaxed fit yang nyaman tanpa unsur batik.',
            'image_url' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=900&q=80',
            'is_active' => true,
        ]);
        foreach ([['Broken White', '#F3F1EC'], ['Dusty Pink', '#C9969B'], ['Sage', '#6F8F7E']] as [$cName, $cHex]) {
            ProductVariant::create([
                'product_id' => $p3->id,
                'color_name' => $cName,
                'color_hex' => $cHex,
                'is_active' => true,
            ]);
        }
        PoBatch::create([
            'product_id' => $p3->id,
            'batch_code' => 'PO-12-BLOUSE',
            'title' => 'Batch PO #12 — Blouse Linen',
            'start_at' => now()->subDays(3),
            'end_at' => now()->addDays(11),
            'quota_total' => 100,
            'allocated_units' => 82,
            'status' => 'open',
            'production_estimate' => '10-14 hari kerja setelah batch tutup',
        ]);

        // Produk 4: Alaiya Inner Ciput Nyaman (Batch Tutup untuk demo AC-02)
        $p4 = Product::create([
            'name' => 'Alaiya Inner Ciput Nyaman',
            'slug' => 'alaiya-inner-ciput-nyaman',
            'category' => 'Aksesoris Hijab',
            'price' => 25000,
            'material' => 'Kaos Rayon Spandex Premium',
            'dimensions' => 'All Size (Elastic Fit, Anti Pusing)',
            'description' => 'Inner ciput harian berbahan rayon spandex yang adem dan tidak menekan telinga. Batch PO #11 telah ditutup dan kuota terpenuhi.',
            'image_url' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=900&q=80',
            'is_active' => true,
        ]);
        foreach ([['Nude', '#D6BFA9'], ['Charcoal', '#36393F'], ['Black', '#18181B']] as [$cName, $cHex]) {
            ProductVariant::create([
                'product_id' => $p4->id,
                'color_name' => $cName,
                'color_hex' => $cHex,
                'is_active' => true,
            ]);
        }
        PoBatch::create([
            'product_id' => $p4->id,
            'batch_code' => 'PO-11-CIPUT',
            'title' => 'Batch PO #11 — Inner Ciput (Tutup)',
            'start_at' => now()->subDays(20),
            'end_at' => now()->subDays(5),
            'quota_total' => 100,
            'allocated_units' => 100,
            'status' => 'closed',
            'production_estimate' => 'Sedang dalam tahap distribusi',
        ]);

        // Sample Order 1: Menunggu Verifikasi (Siti)
        $o1 = Order::create([
            'order_number' => 'PO-2026-001',
            'user_id' => $siti->id,
            'po_batch_id' => $b1->id,
            'recipient_name' => $siti->name,
            'recipient_phone' => $siti->phone,
            'shipping_address' => $siti->address,
            'postal_code' => $siti->postal_code,
            'courier' => 'JNE Reguler',
            'shipping_cost' => 12000,
            'subtotal' => 98000,
            'total_amount' => 110000,
            'payment_deadline' => now()->addHours(20),
            'status' => Order::STATUS_WAITING_VERIFICATION,
        ]);
        OrderItem::create([
            'order_id' => $o1->id,
            'product_variant_id' => $v1Models[0]->id,
            'product_name_snapshot' => $p1->name,
            'variant_name_snapshot' => $v1Models[0]->color_name,
            'unit_price' => 49000,
            'quantity' => 2,
            'line_total' => 98000,
        ]);
        Payment::create([
            'order_id' => $o1->id,
            'proof_path' => 'sample-proof-po-001.jpg',
            'amount' => 110000,
            'status' => 'pending',
        ]);
        OrderStatusHistory::create([
            'order_id' => $o1->id,
            'from_status' => null,
            'to_status' => Order::STATUS_WAITING_PAYMENT,
            'changed_by' => $siti->id,
            'note' => 'Pesanan dibuat, kuota 2 pcs dicadangkan.',
            'created_at' => now()->subHours(2),
        ]);
        OrderStatusHistory::create([
            'order_id' => $o1->id,
            'from_status' => Order::STATUS_WAITING_PAYMENT,
            'to_status' => Order::STATUS_WAITING_VERIFICATION,
            'changed_by' => $siti->id,
            'note' => 'Pelanggan mengunggah bukti transfer BCA Rp 110.000.',
            'created_at' => now()->subHour(),
        ]);

        // Sample Order 2: Dibayar (Rina)
        $o2 = Order::create([
            'order_number' => 'PO-2026-002',
            'user_id' => $rina->id,
            'po_batch_id' => $b2->id,
            'recipient_name' => $rina->name,
            'recipient_phone' => $rina->phone,
            'shipping_address' => $rina->address,
            'postal_code' => $rina->postal_code,
            'courier' => 'J&T Express',
            'shipping_cost' => 12000,
            'subtotal' => 110000,
            'total_amount' => 122000,
            'payment_deadline' => now()->subHours(5),
            'status' => Order::STATUS_PAID,
        ]);
        OrderItem::create([
            'order_id' => $o2->id,
            'product_variant_id' => $v2Models[0]->id,
            'product_name_snapshot' => $p2->name,
            'variant_name_snapshot' => $v2Models[0]->color_name,
            'unit_price' => 55000,
            'quantity' => 2,
            'line_total' => 110000,
        ]);
        Payment::create([
            'order_id' => $o2->id,
            'proof_path' => 'sample-proof-po-002.jpg',
            'amount' => 122000,
            'status' => 'approved',
            'verified_by' => $admin->id,
            'verified_at' => now()->subHours(6),
        ]);
        OrderStatusHistory::create([
            'order_id' => $o2->id,
            'from_status' => null,
            'to_status' => Order::STATUS_WAITING_PAYMENT,
            'changed_by' => $rina->id,
            'note' => 'Pesanan dibuat, kuota 2 pcs dicadangkan.',
            'created_at' => now()->subHours(8),
        ]);
        OrderStatusHistory::create([
            'order_id' => $o2->id,
            'from_status' => Order::STATUS_WAITING_VERIFICATION,
            'to_status' => Order::STATUS_PAID,
            'changed_by' => $admin->id,
            'note' => 'Pembayaran diverifikasi & disetujui oleh Admin.',
            'created_at' => now()->subHours(6),
        ]);

        // Sample Live Chat Conversation (Siti)
        $conv = Conversation::create(['user_id' => $siti->id]);
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $siti->id,
            'body' => 'Halo Admin, untuk Alaiya Daily Voal Scarf warna Sage Green ukurannya berapa cm ya?',
            'read_at' => now()->subMinutes(50),
        ]);
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $admin->id,
            'body' => 'Halo Kak Siti! Ukurannya 115 × 115 cm (all size standar harian), bahan Voal Ultrafine adem & tidak menerawang. Kuota Batch PO #12 masih tersisa 65 pcs ya.',
            'read_at' => now()->subMinutes(45),
        ]);
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id' => $siti->id,
            'body' => 'Baik kak, saya sudah konfirmasi PO-2026-001 dan upload bukti transfer BCA.',
            'read_at' => null,
        ]);
    }
}
