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
        Schema::table('biometric_devices', function (Blueprint $table) {
            // Network type: local, remote, internet
            $table->enum('network_type', ['local', 'remote', 'internet'])->default('local')->after('connection_type');
            
            // Public IP for remote/internet connections
            $table->string('public_ip')->nullable()->after('device_ip');
            
            // VPN/Tunnel configuration
            $table->boolean('requires_vpn')->default(false)->after('public_ip');
            $table->string('vpn_endpoint')->nullable()->after('requires_vpn');
            
            // Network gateway/router IP (for port forwarding)
            $table->string('gateway_ip')->nullable()->after('vpn_endpoint');
            
            // Connection timeout (higher for remote connections)
            $table->integer('connection_timeout')->default(10)->after('gateway_ip');
            
            // Network detection
            $table->boolean('auto_detect_network')->default(true)->after('connection_timeout');
            
            // Last network check
            $table->timestamp('last_network_check')->nullable()->after('auto_detect_network');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_devices', function (Blueprint $table) {
            $table->dropColumn([
                'network_type',
                'public_ip',
                'requires_vpn',
                'vpn_endpoint',
                'gateway_ip',
                'connection_timeout',
                'auto_detect_network',
                'last_network_check',
            ]);
        });
    }
};
