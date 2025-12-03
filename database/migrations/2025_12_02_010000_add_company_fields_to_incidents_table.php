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
            // Add company and user relationships
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->after('company_id')->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->after('reported_by')->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->after('assigned_to')->constrained()->onDelete('set null');
            
            // Add title field (for internal incidents)
            $table->string('title')->nullable()->after('incident_type');
            
            // Add workflow fields
            $table->text('resolution_notes')->nullable()->after('actions_taken');
            $table->dateTime('closed_at')->nullable()->after('resolution_notes');
            
            // Update status enum to include 'open'
            // Note: We'll handle enum changes separately if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['reported_by']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['company_id', 'reported_by', 'assigned_to', 'department_id', 'title', 'resolution_notes', 'closed_at']);
        });
    }
};

