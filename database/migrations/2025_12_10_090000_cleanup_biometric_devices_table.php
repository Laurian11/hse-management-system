<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cleanup: Remove unused fields that are not referenced in any service logic
     * Note: SQLite doesn't support DROP COLUMN directly, so we use raw SQL
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support DROP COLUMN - skip for SQLite
            // The columns will remain but won't be used in the application
            return;
        }
        
        Schema::table('biometric_devices', function (Blueprint $table) {
            // Remove unused VPN and gateway fields
            // These were added but never implemented in service logic
            if (Schema::hasColumn('biometric_devices', 'vpn_endpoint')) {
                $table->dropColumn('vpn_endpoint');
            }
            if (Schema::hasColumn('biometric_devices', 'gateway_ip')) {
                $table->dropColumn('gateway_ip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support adding columns after specific columns
            return;
        }
        
        Schema::table('biometric_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('biometric_devices', 'vpn_endpoint')) {
                $table->string('vpn_endpoint')->nullable()->after('requires_vpn');
            }
            if (!Schema::hasColumn('biometric_devices', 'gateway_ip')) {
                $table->string('gateway_ip')->nullable()->after('vpn_endpoint');
            }
        });
    }
};

