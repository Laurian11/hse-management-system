<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inspection_schedules')) {
            return;
        }
        
        Schema::create('inspection_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'annually', 'custom'])->default('monthly');
            $table->integer('custom_days')->nullable(); // For custom frequency
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_inspection_date');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('inspection_checklist_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'next_inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_schedules');
    }
};
