<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_permit_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_permit_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->integer('approval_level'); // 1, 2, 3, etc.
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'delegated'])->default('pending');
            $table->text('comments')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->foreignId('delegated_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['work_permit_id', 'approval_level']);
            $table->index(['company_id', 'approver_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_approvals');
    }
};
