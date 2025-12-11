<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('work_permits')) {
            return;
        }
        
        Schema::create('work_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            // Note: Foreign key will be added after work_permit_types table is created
            $table->unsignedBigInteger('work_permit_type_id')->nullable();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Work Details
            $table->string('work_title');
            $table->text('work_description');
            $table->string('work_location');
            $table->dateTime('work_start_date');
            $table->dateTime('work_end_date');
            $table->integer('validity_hours');
            $table->dateTime('expiry_date');
            
            // Risk Assessment & JSA Links
            $table->foreignId('risk_assessment_id')->nullable()->constrained('risk_assessments')->onDelete('set null');
            $table->foreignId('jsa_id')->nullable()->constrained('jsas')->onDelete('set null');
            
            // Safety Requirements
            $table->json('safety_precautions')->nullable();
            $table->json('required_equipment')->nullable();
            $table->boolean('gas_test_required')->default(false);
            $table->text('gas_test_results')->nullable();
            $table->dateTime('gas_test_date')->nullable();
            $table->foreignId('gas_tester_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('fire_watch_required')->default(false);
            $table->foreignId('fire_watch_person_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Approval Workflow
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'active', 'suspended', 'expired', 'closed', 'cancelled'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Closure & Verification
            $table->dateTime('actual_start_date')->nullable();
            $table->dateTime('actual_end_date')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('closed_at')->nullable();
            $table->text('closure_notes')->nullable();
            $table->boolean('verification_completed')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // GCLA Compliance
            $table->boolean('gcla_compliance_required')->default(false);
            $table->text('gcla_compliance_notes')->nullable();
            
            // Additional Information
            $table->json('workers')->nullable(); // List of workers assigned
            $table->json('supervisors')->nullable(); // List of supervisors
            $table->text('emergency_procedures')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'work_permit_type_id']);
            $table->index(['company_id', 'requested_by']);
            $table->index('expiry_date');
            $table->index('work_start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permits');
    }
};
