<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('first_aid_logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->foreignId('injured_person_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('injured_person_name')->nullable();
            $table->string('location')->nullable();
            $table->text('nature_of_injury')->nullable();
            $table->text('first_aid_provided')->nullable();
            $table->enum('severity', ['minor', 'moderate', 'serious'])->default('minor');
            $table->boolean('referred_to_medical')->default(false);
            $table->string('medical_facility')->nullable();
            $table->foreignId('first_aider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('treatment_details')->nullable();
            $table->text('follow_up_required')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'incident_date']);
            $table->index(['company_id', 'injured_person_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('first_aid_logbook_entries');
    }
};
