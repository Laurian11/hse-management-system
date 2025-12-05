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
        Schema::create('training_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('training_record_id')->nullable()->constrained('training_records')->onDelete('set null');
            $table->foreignId('training_session_id')->nullable()->constrained('training_sessions')->onDelete('set null');
            $table->foreignId('competency_assessment_id')->nullable()->constrained('competency_assessments')->onDelete('set null');
            
            // Certificate Details
            $table->string('certificate_title');
            $table->text('certificate_description')->nullable();
            $table->enum('certificate_type', [
                'completion',
                'competency',
                'qualification',
                'refresher',
                'regulatory',
                'custom'
            ])->default('completion');
            
            // Issue Information
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('has_expiry')->default(false);
            $table->integer('validity_months')->nullable(); // If expiry is calculated
            
            // Issuer
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->string('issuing_organization')->nullable();
            $table->string('issuing_authority')->nullable();
            
            // Digital Certificate
            $table->string('certificate_file_path')->nullable(); // PDF or image file
            $table->string('digital_signature')->nullable();
            $table->string('verification_code')->unique()->nullable(); // For online verification
            
            // Status
            $table->enum('status', [
                'active',
                'expired',
                'revoked',
                'suspended'
            ])->default('active');
            
            $table->text('revocation_reason')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('revoked_at')->nullable();
            
            // Alert Settings
            $table->boolean('expiry_alert_sent_60_days')->default(false);
            $table->boolean('expiry_alert_sent_30_days')->default(false);
            $table->boolean('expiry_alert_sent_7_days')->default(false);
            $table->timestamp('last_alert_sent_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'user_id']);
            $table->index('expiry_date');
            $table->index('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_certificates');
    }
};
