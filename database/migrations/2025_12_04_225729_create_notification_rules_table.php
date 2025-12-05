<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['incident', 'expiring_permit', 'expiring_ppe', 'expiring_training', 'expiring_certificate', 'overdue_inspection', 'compliance_deadline', 'custom'])->default('custom');
            $table->enum('notification_channel', ['email', 'sms', 'push', 'all'])->default('email');
            $table->json('recipients')->nullable(); // User IDs, roles, or email addresses
            $table->json('conditions')->nullable(); // Conditions for triggering
            $table->integer('days_before')->nullable(); // Days before event (for expiring items)
            $table->integer('days_after')->nullable(); // Days after event (for overdue items)
            $table->enum('frequency', ['once', 'daily', 'weekly', 'monthly', 'custom'])->default('once');
            $table->time('notification_time')->nullable(); // Time of day to send
            $table->text('message_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'trigger_type']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_rules');
    }
};
