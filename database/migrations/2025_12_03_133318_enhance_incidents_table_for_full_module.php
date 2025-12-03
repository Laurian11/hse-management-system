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
            // Enhanced Event Type Classification
            $table->enum('event_type', ['injury_illness', 'property_damage', 'near_miss', 'environmental', 'security', 'other'])->nullable()->after('incident_type');
            
            // Injury/Illness Specific Fields
            $table->enum('injury_type', ['fatal', 'lost_time', 'medical_treatment', 'first_aid', 'illness', 'other'])->nullable();
            $table->text('body_part_affected')->nullable();
            $table->text('nature_of_injury')->nullable();
            $table->boolean('lost_time_injury')->default(false);
            $table->integer('days_lost')->nullable();
            $table->text('medical_treatment_details')->nullable();
            
            // Property Damage Specific Fields
            $table->text('asset_damaged')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->text('damage_description')->nullable();
            $table->boolean('insurance_claim_filed')->default(false);
            
            // Near Miss Specific Fields
            $table->text('potential_severity')->nullable();
            $table->text('potential_consequences')->nullable();
            $table->text('preventive_measures_taken')->nullable();
            
            // Enhanced Location Details
            $table->string('location_specific')->nullable(); // More specific location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Workflow and Closure
            $table->enum('closure_status', ['pending_approval', 'approved', 'rejected', 'closed'])->nullable()->after('status');
            $table->json('approval_workflow')->nullable(); // Multi-step approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Investigation Link
            $table->foreignId('investigation_id')->nullable()->constrained('incident_investigations')->onDelete('set null');
            
            // RCA Link
            $table->foreignId('root_cause_analysis_id')->nullable()->constrained('root_cause_analyses')->onDelete('set null');
            
            // Additional Fields
            $table->text('regulatory_reporting_required')->nullable();
            $table->boolean('reported_to_authorities')->default(false);
            $table->dateTime('reported_to_authorities_at')->nullable();
            $table->text('regulatory_reference')->nullable();
            
            // Indexes
            $table->index('event_type');
            $table->index('closure_status');
            $table->index('investigation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['investigation_id']);
            $table->dropForeign(['root_cause_analysis_id']);
            $table->dropForeign(['approved_by']);
            
            $table->dropColumn([
                'event_type',
                'injury_type',
                'body_part_affected',
                'nature_of_injury',
                'lost_time_injury',
                'days_lost',
                'medical_treatment_details',
                'asset_damaged',
                'estimated_cost',
                'damage_description',
                'insurance_claim_filed',
                'potential_severity',
                'potential_consequences',
                'preventive_measures_taken',
                'location_specific',
                'latitude',
                'longitude',
                'closure_status',
                'approval_workflow',
                'approved_by',
                'approved_at',
                'rejection_reason',
                'investigation_id',
                'root_cause_analysis_id',
                'regulatory_reporting_required',
                'reported_to_authorities',
                'reported_to_authorities_at',
                'regulatory_reference',
            ]);
        });
    }
};
