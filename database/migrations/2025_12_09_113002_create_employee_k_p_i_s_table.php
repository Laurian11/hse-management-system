<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->date('recorded_date');
            $table->string('period_type', 20)->default('daily'); // daily, weekly, monthly, yearly
            
            // Attendance Metrics
            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('days_late')->default(0);
            $table->integer('days_early_leave')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->decimal('punctuality_rate', 5, 2)->default(0);
            $table->integer('total_hours_worked')->default(0);
            $table->integer('overtime_hours')->default(0);
            
            // Safety Metrics
            $table->integer('incidents_involved')->default(0);
            $table->integer('near_misses_reported')->default(0);
            $table->integer('safety_observations')->default(0);
            $table->integer('safety_violations')->default(0);
            $table->decimal('safety_score', 5, 2)->default(100);
            
            // Training Metrics
            $table->integer('trainings_completed')->default(0);
            $table->integer('trainings_pending')->default(0);
            $table->integer('trainings_overdue')->default(0);
            $table->integer('certificates_earned')->default(0);
            $table->integer('certificates_expired')->default(0);
            $table->integer('certificates_expiring_soon')->default(0);
            $table->decimal('training_completion_rate', 5, 2)->default(0);
            $table->decimal('certification_compliance', 5, 2)->default(0);
            
            // PPE Compliance
            $table->integer('ppe_items_issued')->default(0);
            $table->integer('ppe_items_due_for_replacement')->default(0);
            $table->integer('ppe_inspections_completed')->default(0);
            $table->integer('ppe_inspections_missed')->default(0);
            $table->decimal('ppe_compliance_rate', 5, 2)->default(0);
            
            // Toolbox Talks
            $table->integer('toolbox_talks_attended')->default(0);
            $table->integer('toolbox_talks_missed')->default(0);
            $table->integer('toolbox_talks_scheduled')->default(0);
            $table->decimal('toolbox_talk_attendance_rate', 5, 2)->default(0);
            
            // Performance Metrics
            $table->integer('tasks_assigned')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_overdue')->default(0);
            $table->decimal('task_completion_rate', 5, 2)->default(0);
            $table->decimal('average_task_completion_time', 8, 2)->default(0); // hours
            $table->integer('quality_issues')->default(0);
            $table->decimal('quality_score', 5, 2)->default(100);
            
            // Health & Wellness
            $table->integer('medical_examinations_completed')->default(0);
            $table->integer('medical_examinations_due')->default(0);
            $table->integer('sick_leave_days')->default(0);
            $table->integer('first_aid_incidents')->default(0);
            $table->decimal('health_compliance_rate', 5, 2)->default(0);
            
            // Compliance Metrics
            $table->integer('compliance_requirements_met')->default(0);
            $table->integer('compliance_requirements_total')->default(0);
            $table->integer('non_compliances')->default(0);
            $table->decimal('overall_compliance_rate', 5, 2)->default(0);
            
            // Engagement Metrics
            $table->integer('suggestions_submitted')->default(0);
            $table->integer('suggestions_implemented')->default(0);
            $table->integer('feedback_provided')->default(0);
            $table->decimal('engagement_score', 5, 2)->default(0);
            
            // Overall Score
            $table->decimal('overall_performance_score', 5, 2)->default(0); // 0-100
            $table->string('performance_rating', 20)->nullable(); // Excellent, Good, Fair, Poor, Needs Improvement
            $table->text('performance_notes')->nullable();
            
            $table->timestamps();
            
            $table->unique(['employee_id', 'recorded_date', 'period_type']);
            $table->index(['employee_id', 'recorded_date']);
            $table->index(['company_id', 'recorded_date']);
            $table->index(['department_id', 'recorded_date']);
            $table->index(['period_type', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_kpis');
    }
};
