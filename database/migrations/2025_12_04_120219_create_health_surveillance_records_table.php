<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('health_surveillance_records')) {
            return;
        }
        
        Schema::create('health_surveillance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('surveillance_type', ['medical_examination', 'health_test', 'vaccination', 'audiometry', 'lung_function', 'vision_test', 'blood_test', 'other']);
            $table->string('examination_name')->nullable();
            $table->date('examination_date');
            $table->date('next_due_date')->nullable();
            $table->foreignId('medical_provider_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('provider_name')->nullable();
            $table->text('findings')->nullable();
            $table->enum('result', ['fit', 'unfit', 'fit_with_restrictions', 'requires_follow_up', 'pending'])->default('pending');
            $table->text('restrictions')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('conducted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'user_id']);
            $table->index(['company_id', 'examination_date']);
            $table->index(['company_id', 'next_due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_surveillance_records');
    }
};
