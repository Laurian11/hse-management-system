<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('waste_management_records')) {
            return;
        }
        
        Schema::create('waste_management_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('waste_type'); // hazardous, non_hazardous, recyclable, organic, etc.
            $table->string('category')->nullable(); // chemical, biological, electronic, etc.
            $table->text('description')->nullable();
            $table->enum('segregation_status', ['properly_segregated', 'improperly_segregated', 'mixed'])->default('properly_segregated');
            $table->string('storage_location')->nullable();
            $table->enum('storage_method', ['container', 'tank', 'drum', 'bag', 'other'])->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable(); // kg, liters, cubic_meters, etc.
            $table->date('collection_date')->nullable();
            $table->date('disposal_date')->nullable();
            $table->enum('disposal_method', ['landfill', 'incineration', 'recycling', 'treatment', 'reuse', 'other'])->nullable();
            $table->foreignId('disposal_contractor_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('disposal_certificate_number')->nullable();
            $table->date('disposal_certificate_date')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'waste_type']);
            $table->index(['company_id', 'disposal_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_management_records');
    }
};
