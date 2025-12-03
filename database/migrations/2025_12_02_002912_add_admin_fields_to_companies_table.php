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
        Schema::table('companies', function (Blueprint $table) {
            // Company Settings & Configuration
            $table->json('settings')->nullable()->after('description');
            $table->string('timezone', 50)->default('UTC')->after('settings');
            $table->string('currency', 3)->default('USD')->after('timezone');
            $table->string('language', 10)->default('en')->after('currency');
            
            // Licensing & Subscription
            $table->string('license_type', 20)->default('basic')->after('language');
            $table->date('license_expiry')->nullable()->after('license_type');
            $table->integer('max_users')->default(50)->after('license_expiry');
            $table->integer('max_departments')->default(10)->after('max_users');
            $table->json('features')->nullable()->after('max_departments');
            
            // Contact Information (replacing existing contact fields)
            $table->string('phone')->nullable()->after('features');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->text('address')->nullable()->after('website');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            
            // HSE Specific Data
            $table->json('hse_policies')->nullable()->after('postal_code');
            $table->json('safety_standards')->nullable()->after('hse_policies');
            $table->string('industry_type')->nullable()->after('safety_standards');
            $table->integer('employee_count')->default(0)->after('industry_type');
            $table->json('compliance_certifications')->nullable()->after('employee_count');
            
            // Status & Management (only add soft deletes, is_active already exists)
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            $table->softDeletes()->after('deactivation_reason');
            
            // Indexes
            $table->index(['license_type']);
            $table->index(['license_expiry']);
            $table->index(['country']);
            $table->index(['industry_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'settings',
                'timezone',
                'currency',
                'language',
                'license_type',
                'license_expiry',
                'max_users',
                'max_departments',
                'features',
                'phone',
                'email',
                'website',
                'address',
                'city',
                'state',
                'country',
                'postal_code',
                'hse_policies',
                'safety_standards',
                'industry_type',
                'employee_count',
                'compliance_certifications',
                'deactivated_at',
                'deactivation_reason'
            ]);
        });
    }
};
