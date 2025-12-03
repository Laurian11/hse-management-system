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
        Schema::create('safety_communications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable();
            $table->enum('communication_type', ['announcement', 'alert', 'bulletin', 'emergency', 'reminder', 'policy_update', 'training_notice'])->default('announcement');
            $table->enum('priority_level', ['low', 'medium', 'high', 'critical', 'emergency'])->default('medium');
            $table->string('title');
            $table->text('message');
            $table->json('attachments')->nullable(); // Documents, images, videos
            $table->enum('target_audience', ['all_employees', 'specific_departments', 'specific_roles', 'specific_locations', 'management_only', 'supervisors_only'])->default('all_employees');
            $table->json('target_departments')->nullable(); // Array of department IDs
            $table->json('target_roles')->nullable(); // Array of role names
            $table->json('target_locations')->nullable(); // Array of location names
            $table->enum('delivery_method', ['digital_signage', 'mobile_push', 'email', 'sms', 'printed_notice', 'video_conference', 'in_person'])->default('digital_signage');
            $table->json('delivery_channels')->nullable(); // Multiple delivery methods
            $table->boolean('requires_acknowledgment')->default(false);
            $table->dateTime('acknowledgment_deadline')->nullable();
            $table->dateTime('scheduled_send_time')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('expires_at')->nullable(); // For time-sensitive communications
            $table->enum('status', ['draft', 'scheduled', 'sent', 'expired', 'cancelled'])->default('draft');
            $table->integer('total_recipients')->default(0);
            $table->integer('acknowledged_count')->default(0);
            $table->integer('read_count')->default(0);
            $table->decimal('acknowledgment_rate', 5, 2)->default(0);
            $table->text('regulatory_reference')->nullable(); // Related regulations
            $table->json('related_incidents')->nullable(); // Links to relevant incidents
            $table->json('quiz_questions')->nullable(); // Knowledge check for critical messages
            $table->boolean('is_multilingual')->default(false);
            $table->json('translations')->nullable(); // Multiple language versions
            $table->string('language')->default('en');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_communications');
    }
};
