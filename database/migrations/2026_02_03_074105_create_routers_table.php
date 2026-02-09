<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('routers', function (Blueprint $table) {
        $table->id();
        $table->string('name');             // e.g. "My Home Router"
        $table->string('vpn_ip');           // The 10.0.0.x IP
        $table->string('local_ip');         // Your router's home IP (e.g. 192.168.88.1)
        $table->string('api_user');         // Router login
        $table->string('api_password');     // Router password
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
