<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('Scarf Harian');
            $table->unsignedInteger('price');
            $table->string('material');
            $table->string('dimensions');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('color_name');
            $table->string('color_hex', 20)->default('#2D5A4C');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('po_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_code');
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('quota_total')->default(100);
            $table->unsignedInteger('allocated_units')->default(0);
            $table->string('status')->default('open'); // scheduled, open, closed
            $table->string('production_estimate')->default('7-10 hari kerja setelah batch tutup');
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('po_batch_id')->constrained()->restrictOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_phone', 30);
            $table->text('shipping_address');
            $table->string('postal_code', 20);
            $table->string('courier')->default('JNE Reguler');
            $table->unsignedInteger('shipping_cost')->default(12000);
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('total_amount');
            $table->dateTime('payment_deadline');
            $table->string('status')->default('menunggu_pembayaran');
            $table->string('tracking_number')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['po_batch_id', 'status']);
            $table->index('user_id');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $table->string('product_name_snapshot');
            $table->string('variant_name_snapshot');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('line_total');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('proof_path');
            $table->unsignedInteger('amount');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'verified_at']);
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('po_batches');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
