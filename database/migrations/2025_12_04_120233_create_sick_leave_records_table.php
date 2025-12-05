<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sick_leave_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('leave_start_date');
            $table->date('leave_end_date')->nullable();
            $table->integer('days_absent')->nullable();
            $table->enum('leave_type', ['sick_leave', 'medical_leave', 'injury_leave', 'work_related_injury'])->default('sick_leave');
            $table->text('reason')->nullable();
            $table->text('medical_certificate_provided')->nullable();
            $table->boolean('work_related')->default(false);
            $table->foreignId('related_incident_id')->nullable()->constrained('incidents')->onDelete('set null');
            $table->foreignId('related_first_aid_id')->nullable()->constrained('first_aid_logbook_entries')->onDelete('set null');
            $table->text('treatment_received')->nullable();
            $table->text('follow_up_required')->nullable();
            $table->date('return_to_work_date')->nullable();
            $table->enum('return_to_work_status', ['full_duty', 'light_duty', 'restricted', 'pending_clearance'])->nullable();
            $table->text('medical_clearance_notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'user_id']);
            $table->index(['company_id', 'leave_start_date']);
            $table->index(['company_id', 'work_related']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sick_leave_records');
    }
};
