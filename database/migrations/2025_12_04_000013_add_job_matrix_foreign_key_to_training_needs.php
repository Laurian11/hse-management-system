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
        Schema::table('training_needs_analyses', function (Blueprint $table) {
            $table->foreign('triggered_by_job_matrix_id')->references('id')->on('job_competency_matrices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_needs_analyses', function (Blueprint $table) {
            $table->dropForeign(['triggered_by_job_matrix_id']);
        });
    }
};
