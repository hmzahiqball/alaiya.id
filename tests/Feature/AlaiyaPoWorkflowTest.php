<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PoBatch;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlaiyaPoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_view_catalog_and_product_detail(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('alaiya.id');
        $this->get(route('catalog.index'))->assertOk()->assertSee('Alaiya Daily Voal Scarf');

        $product = Product::where('slug', 'alaiya-daily-voal-scarf')->firstOrFail();
        $this->get(route('catalog.show', $product))->assertOk()->assertSee('115 cm');
    }

    public function test_guest_and_customer_cannot_access_admin_routes(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));

        $customer = User::where('email', 'siti@mahasiswa.ac.id')->firstOrFail();
        $this->actingAs($customer)->get(route('admin.products.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.reports.export-sales'))->assertForbidden();
    }

    public function test_customer_can_checkout_po_and_reserve_quota(): void
    {
        $customer = User::where('email', 'siti@mahasiswa.ac.id')->firstOrFail();
        $product = Product::with(['activeVariants', 'openBatch'])->where('slug', 'alaiya-daily-voal-scarf')->firstOrFail();
        $initialAllocated = $product->openBatch->allocated_units;

        $this->actingAs($customer)->post(route('checkout.add'), [
            'product_id' => $product->id,
            'variant_id' => $product->activeVariants->first()->id,
            'quantity' => 3,
        ])->assertRedirect(route('checkout.index'));

        $this->actingAs($customer)->post(route('checkout.store'), [
            'recipient_name' => 'Siti Aisyah',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Gegerkalong No. 45',
            'postal_code' => '40153',
            'courier' => 'JNE Reguler',
        ])->assertRedirect();

        $this->assertEquals($initialAllocated + 3, $product->openBatch->fresh()->allocated_units);
    }

    public function test_admin_cannot_lower_quota_below_allocated_units(): void
    {
        $admin = User::where('email', 'admin@alaiya.id')->firstOrFail();
        $batch = PoBatch::where('batch_code', 'PO-12-VOAL')->firstOrFail();

        $this->actingAs($admin)->patch(route('admin.batches.update', $batch), [
            'quota_total' => 10,
            'status' => 'open',
        ])->assertSessionHas('error');

        $this->assertEquals(100, $batch->fresh()->quota_total);
    }

    public function test_customer_cannot_view_other_customers_order(): void
    {
        $siti = User::where('email', 'siti@mahasiswa.ac.id')->firstOrFail();
        $rinaOrder = Order::where('order_number', 'PO-2026-002')->firstOrFail();

        $this->actingAs($siti)->get(route('orders.show', $rinaOrder))->assertForbidden();
    }

    public function test_live_chat_persists_messages_between_customer_and_admin(): void
    {
        $siti = User::where('email', 'siti@mahasiswa.ac.id')->firstOrFail();

        $this->actingAs($siti)->post(route('chat.send'), [
            'body' => 'Apakah warna Navy masih tersedia?',
        ])->assertRedirect();

        $this->actingAs($siti)->get(route('chat.index'))
            ->assertOk()
            ->assertSee('Apakah warna Navy masih tersedia?');
    }
}
