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
        // Add training-related fields to control_measures table
        Schema::table('control_measures', function (Blueprint $table) {
            $table->foreignId('related_training_need_id')->nullable()->after('related_capa_id')
                ->constrained('training_needs_analyses')->onDelete('set null');
            $table->foreignId('related_training_plan_id')->nullable()->after('related_training_need_id')
                ->constrained('training_plans')->onDelete('set null');
            $table->boolean('training_required')->default(false)->after('control_type');
            $table->boolean('training_verified')->default(false)->after('training_required');
            $table->timestamp('training_verified_at')->nullable()->after('training_verified');
        });
        
        // Add training-related fields to capas table
        Schema::table('capas', function (Blueprint $table) {
            $table->foreignId('related_training_need_id')->nullable()->after('root_cause_analysis_id')
                ->constrained('training_needs_analyses')->onDelete('set null');
            $table->foreignId('related_training_plan_id')->nullable()->after('related_training_need_id')
                ->constrained('training_plans')->onDelete('set null');
            $table->boolean('training_completed')->default(false)->after('effectiveness');
            $table->timestamp('training_completed_at')->nullable()->after('training_completed');
        });
        
        // Add training-related fields to root_cause_analyses table
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            $table->boolean('training_gap_identified')->default(false)->after('prevention_possible');
            $table->text('training_gap_description')->nullable()->after('training_gap_identified');
        });
        
        // Add training-related fields to users table (if not already exists)
        if (!Schema::hasColumn('users', 'job_role_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('job_role_code')->nullable()->after('job_title');
                $table->foreignId('job_competency_matrix_id')->nullable()->after('job_role_code')
                    ->constrained('job_competency_matrices')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('control_measures', function (Blueprint $table) {
            $table->dropForeign(['related_training_need_id']);
            $table->dropForeign(['related_training_plan_id']);
            $table->dropColumn([
                'related_training_need_id',
                'related_training_plan_id',
                'training_required',
                'training_verified',
                'training_verified_at'
            ]);
        });
        
        Schema::table('capas', function (Blueprint $table) {
            $table->dropForeign(['related_training_need_id']);
            $table->dropForeign(['related_training_plan_id']);
            $table->dropColumn([
                'related_training_need_id',
                'related_training_plan_id',
                'training_completed',
                'training_completed_at'
            ]);
        });
        
        Schema::table('root_cause_analyses', function (Blueprint $table) {
            $table->dropColumn(['training_gap_identified', 'training_gap_description']);
        });
        
        if (Schema::hasColumn('users', 'job_role_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['job_competency_matrix_id']);
                $table->dropColumn(['job_role_code', 'job_competency_matrix_id']);
            });
        }
    }
};
