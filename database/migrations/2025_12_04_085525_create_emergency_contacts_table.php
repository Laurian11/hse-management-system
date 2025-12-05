<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('contact_type', ['internal', 'external', 'emergency_services', 'medical', 'fire', 'police', 'hazmat', 'management'])->default('internal');
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->string('phone_primary');
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('special_instructions')->nullable();
            $table->integer('priority')->default(0); // For sorting
            $table->boolean('is_active')->default(true);
            $table->boolean('is_24_7')->default(false); // Available 24/7
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'contact_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
