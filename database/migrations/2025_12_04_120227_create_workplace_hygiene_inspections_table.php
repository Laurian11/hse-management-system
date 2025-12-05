<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workplace_hygiene_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('inspection_date');
            $table->string('area_inspected')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('inspection_items')->nullable(); // toilets, kitchens, waste_disposal, ventilation, etc.
            $table->json('findings')->nullable(); // compliant, non_compliant, needs_improvement
            $table->text('non_compliance_details')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->date('corrective_action_due_date')->nullable();
            $table->foreignId('corrective_action_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('corrective_action_completed')->default(false);
            $table->date('corrective_action_completed_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('verification_date')->nullable();
            $table->enum('overall_status', ['satisfactory', 'needs_improvement', 'unsatisfactory'])->default('satisfactory');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'inspection_date']);
            $table->index(['company_id', 'overall_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workplace_hygiene_inspections');
    }
};
