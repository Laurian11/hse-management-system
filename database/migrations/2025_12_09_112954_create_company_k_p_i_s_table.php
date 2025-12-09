<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('recorded_date');
            $table->string('period_type', 20)->default('daily'); // daily, weekly, monthly, yearly
            
            // Employee Metrics
            $table->integer('total_employees')->default(0);
            $table->integer('active_employees')->default(0);
            $table->integer('new_employees')->default(0);
            $table->integer('terminated_employees')->default(0);
            $table->decimal('employee_turnover_rate', 5, 2)->default(0);
            
            // Incident Metrics
            $table->integer('total_incidents')->default(0);
            $table->integer('incidents_this_period')->default(0);
            $table->integer('incidents_resolved')->default(0);
            $table->integer('incidents_pending')->default(0);
            $table->decimal('incident_rate', 8, 2)->default(0); // per 1000 employees
            $table->decimal('incident_resolution_rate', 5, 2)->default(0);
            
            // Training Metrics
            $table->integer('total_trainings')->default(0);
            $table->integer('trainings_completed')->default(0);
            $table->integer('trainings_pending')->default(0);
            $table->decimal('training_completion_rate', 5, 2)->default(0);
            $table->integer('employees_trained')->default(0);
            $table->decimal('training_coverage', 5, 2)->default(0); // % of employees trained
            
            // Compliance Metrics
            $table->integer('total_audits')->default(0);
            $table->integer('audits_passed')->default(0);
            $table->integer('audits_failed')->default(0);
            $table->decimal('compliance_rate', 5, 2)->default(0);
            $table->integer('non_conformances')->default(0);
            $table->integer('non_conformances_resolved')->default(0);
            
            // Risk Assessment Metrics
            $table->integer('total_risk_assessments')->default(0);
            $table->integer('high_risk_items')->default(0);
            $table->integer('medium_risk_items')->default(0);
            $table->integer('low_risk_items')->default(0);
            $table->decimal('average_risk_score', 5, 2)->default(0);
            
            // PPE Metrics
            $table->integer('ppe_items_issued')->default(0);
            $table->integer('ppe_inspections_due')->default(0);
            $table->integer('ppe_inspections_completed')->default(0);
            $table->decimal('ppe_compliance_rate', 5, 2)->default(0);
            
            // Toolbox Talks Metrics
            $table->integer('toolbox_talks_scheduled')->default(0);
            $table->integer('toolbox_talks_completed')->default(0);
            $table->integer('toolbox_talks_attendance')->default(0);
            $table->decimal('toolbox_talk_completion_rate', 5, 2)->default(0);
            $table->decimal('average_attendance_rate', 5, 2)->default(0);
            
            // Financial Metrics (if applicable)
            $table->decimal('safety_budget_allocated', 15, 2)->nullable();
            $table->decimal('safety_budget_spent', 15, 2)->nullable();
            $table->decimal('safety_budget_utilization', 5, 2)->nullable();
            
            // Overall Score
            $table->decimal('overall_safety_score', 5, 2)->default(0); // Composite score 0-100
            $table->string('safety_rating', 20)->nullable(); // Excellent, Good, Fair, Poor
            
            $table->timestamps();
            
            $table->unique(['company_id', 'recorded_date', 'period_type']);
            $table->index(['company_id', 'recorded_date']);
            $table->index(['period_type', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_kpis');
    }
};
