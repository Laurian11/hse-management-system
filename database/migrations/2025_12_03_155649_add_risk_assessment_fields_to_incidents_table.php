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
        Schema::table('incidents', function (Blueprint $table) {
            // Link to Risk Assessment Module
            $table->foreignId('related_hazard_id')->nullable()->after('closed_at')->constrained('hazards')->onDelete('set null');
            $table->foreignId('related_risk_assessment_id')->nullable()->after('related_hazard_id')->constrained('risk_assessments')->onDelete('set null');
            $table->foreignId('related_jsa_id')->nullable()->after('related_risk_assessment_id')->constrained('jsas')->onDelete('set null');
            
            // Flags for closed-loop integration
            $table->boolean('hazard_was_identified')->default(false)->after('related_jsa_id');
            $table->boolean('controls_were_in_place')->default(false);
            $table->boolean('controls_were_effective')->nullable();
            $table->text('risk_assessment_gap_analysis')->nullable();
            
            // Indexes
            $table->index('related_hazard_id');
            $table->index('related_risk_assessment_id');
            $table->index('related_jsa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['related_hazard_id']);
            $table->dropForeign(['related_risk_assessment_id']);
            $table->dropForeign(['related_jsa_id']);
            $table->dropColumn([
                'related_hazard_id',
                'related_risk_assessment_id',
                'related_jsa_id',
                'hazard_was_identified',
                'controls_were_in_place',
                'controls_were_effective',
                'risk_assessment_gap_analysis'
            ]);
        });
    }
};
