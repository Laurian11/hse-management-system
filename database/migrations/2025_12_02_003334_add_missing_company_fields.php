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
            // Add missing soft delete columns if they don't exist
            if (!Schema::hasColumn('companies', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('compliance_certifications');
            }
            if (!Schema::hasColumn('companies', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            }
            if (!Schema::hasColumn('companies', 'deleted_at')) {
                $table->softDeletes()->after('deactivation_reason');
            }
            
            // Add missing indexes if they don't exist
            if (!Schema::hasIndex('companies', 'companies_license_type_index')) {
                $table->index(['license_type']);
            }
            if (!Schema::hasIndex('companies', 'companies_license_expiry_index')) {
                $table->index(['license_expiry']);
            }
            if (!Schema::hasIndex('companies', 'companies_country_index')) {
                $table->index(['country']);
            }
            if (!Schema::hasIndex('companies', 'companies_industry_type_index')) {
                $table->index(['industry_type']);
            }
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
                'deactivated_at',
                'deactivation_reason'
            ]);
        });
    }
};
