<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('recorded_date');
            $table->string('period_type', 20)->default('daily'); // daily, weekly, monthly, yearly
            
            // Activity Metrics
            $table->integer('total_logins')->default(0);
            $table->integer('total_activities')->default(0);
            $table->integer('activities_this_period')->default(0);
            $table->decimal('average_activities_per_day', 8, 2)->default(0);
            $table->integer('days_active')->default(0);
            $table->decimal('activity_rate', 5, 2)->default(0); // % of days active
            
            // Work Metrics
            $table->integer('incidents_created')->default(0);
            $table->integer('incidents_resolved')->default(0);
            $table->integer('incidents_assigned')->default(0);
            $table->integer('risk_assessments_created')->default(0);
            $table->integer('risk_assessments_reviewed')->default(0);
            $table->integer('trainings_conducted')->default(0);
            $table->integer('trainings_attended')->default(0);
            $table->integer('audits_conducted')->default(0);
            $table->integer('toolbox_talks_conducted')->default(0);
            $table->integer('toolbox_talks_attended')->default(0);
            $table->integer('documents_created')->default(0);
            $table->integer('documents_reviewed')->default(0);
            
            // Performance Metrics
            $table->decimal('incident_resolution_rate', 5, 2)->default(0);
            $table->decimal('average_resolution_time', 8, 2)->default(0); // hours
            $table->decimal('task_completion_rate', 5, 2)->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_pending')->default(0);
            $table->integer('tasks_overdue')->default(0);
            
            // Engagement Metrics
            $table->integer('reports_generated')->default(0);
            $table->integer('exports_performed')->default(0);
            $table->integer('searches_performed')->default(0);
            $table->decimal('session_duration_avg', 8, 2)->default(0); // minutes
            $table->integer('total_session_time')->default(0); // minutes
            
            // Collaboration Metrics
            $table->integer('comments_made')->default(0);
            $table->integer('approvals_given')->default(0);
            $table->integer('assignments_made')->default(0);
            $table->integer('notifications_sent')->default(0);
            
            // Compliance Metrics
            $table->integer('training_certificates_earned')->default(0);
            $table->integer('training_certificates_expired')->default(0);
            $table->integer('ppe_items_issued')->default(0);
            $table->integer('ppe_inspections_completed')->default(0);
            $table->decimal('compliance_score', 5, 2)->default(0);
            
            // Quality Metrics
            $table->integer('errors_made')->default(0);
            $table->integer('corrections_made')->default(0);
            $table->decimal('quality_score', 5, 2)->default(100);
            
            // Overall Score
            $table->decimal('productivity_score', 5, 2)->default(0); // 0-100
            $table->decimal('engagement_score', 5, 2)->default(0); // 0-100
            $table->decimal('overall_performance_score', 5, 2)->default(0); // 0-100
            $table->string('performance_rating', 20)->nullable(); // Excellent, Good, Fair, Poor
            
            $table->timestamps();
            
            $table->unique(['user_id', 'recorded_date', 'period_type']);
            $table->index(['user_id', 'recorded_date']);
            $table->index(['period_type', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_kpis');
    }
};
