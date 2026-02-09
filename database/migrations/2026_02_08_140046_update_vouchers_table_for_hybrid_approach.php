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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('password')->nullable()->after('code');
            $table->integer('duration')->default(60)->after('profile'); // minutes
            $table->string('bandwidth')->default('1M/1M')->after('duration');
            $table->enum('status', ['pending', 'active', 'expired', 'used'])->default('pending')->after('bandwidth');
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->timestamp('redeemed_at')->nullable()->after('used_at');
            $table->string('redeemed_ip')->nullable()->after('redeemed_at');
            $table->string('device_mac')->nullable()->after('redeemed_ip');
            $table->string('device_info')->nullable()->after('device_mac');
            $table->dropColumn('is_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->boolean('is_used')->default(false);
            $table->dropColumn(['password', 'duration', 'bandwidth', 'status', 'expires_at', 'redeemed_at', 'redeemed_ip', 'device_mac', 'device_info']);
        });
    }
};
