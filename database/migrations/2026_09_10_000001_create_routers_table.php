<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g. "AquaGold Main Hotspot"
            $table->string('ip_address');            // MikroTik management IP
            $table->integer('api_port')->default(8728); // 8729 if using API-SSL
            $table->boolean('use_ssl')->default(false);
            $table->string('username');
            $table->text('password_encrypted');      // encrypted via Laravel Crypt
            $table->string('hotspot_server')->default('hotspot1'); // RouterOS hotspot server name
            $table->string('default_profile')->default('default'); // walled-garden-only profile
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
