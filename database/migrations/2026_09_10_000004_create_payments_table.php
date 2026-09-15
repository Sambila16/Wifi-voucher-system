<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->nullable()->constrained();
            $table->foreignId('plan_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'mobile_money', 'card', 'other'])->default('cash');
            $table->enum('gateway', ['manual', 'azampay', 'selcom', 'clickpesa', 'other'])->default('manual');
            $table->string('gateway_reference')->nullable(); // transaction ID from gateway
            $table->string('customer_phone')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('pending');
            $table->foreignId('received_by_admin_id')->nullable(); // for manual/cash sales
            $table->json('gateway_payload')->nullable(); // raw webhook payload for auditing
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'gateway']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
