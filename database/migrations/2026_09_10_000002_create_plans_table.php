<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // "2GB / 24 Hours"
            $table->decimal('price', 10, 2);          // in TZS
            $table->unsignedBigInteger('data_limit_mb')->nullable(); // null = unlimited data
            $table->unsignedInteger('time_limit_minutes')->nullable(); // null = unlimited time
            $table->unsignedInteger('validity_days')->default(1); // voucher expires if unused after N days
            $table->string('mikrotik_profile'); // hotspot user profile name to assign on RouterOS
            $table->unsignedInteger('shared_users')->default(1); // devices allowed per voucher
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
