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
            // Device category/purpose
            $table->enum('device_category', ['attendance', 'toolbox_training', 'both'])->default('attendance')->after('device_type');
            
            // Device purpose description
            $table->text('device_purpose')->nullable()->after('device_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_devices', function (Blueprint $table) {
            $table->dropColumn(['device_category', 'device_purpose']);
        });
    }
};

