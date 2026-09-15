<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // voucher/hotspot username
            $table->string('password')->nullable();  // hotspot password (can equal code)
            $table->foreignId('plan_id')->constrained();
            $table->foreignId('router_id')->constrained();

            // lifecycle: pending (generated, not yet on router) -> issued (pushed to MikroTik)
            // -> active (first login used) -> expired / revoked
            $table->enum('status', ['pending', 'issued', 'active', 'expired', 'revoked'])
                  ->default('pending');

            $table->enum('source', ['manual', 'automatic'])->default('manual');
            $table->foreignId('generated_by_admin_id')->nullable(); // for manual vouchers
            $table->string('customer_phone')->nullable(); // for delivering code / automatic payments

            $table->timestamp('issued_at')->nullable();   // pushed to MikroTik
            $table->timestamp('activated_at')->nullable(); // first successful hotspot login
            $table->timestamp('expires_at')->nullable();   // computed from validity/activation
            $table->timestamp('revoked_at')->nullable();

            $table->string('mac_address')->nullable(); // bound after first use, enforces 1 device
            $table->timestamps();

            $table->index(['status', 'router_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
