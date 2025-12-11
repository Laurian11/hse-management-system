<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('waste_tracking_records')) {
            return;
        }
        
        Schema::create('waste_tracking_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('waste_management_record_id')->nullable()->constrained()->onDelete('set null');
            $table->string('waste_type');
            $table->string('category')->nullable();
            $table->decimal('volume', 10, 2);
            $table->string('unit')->default('kg');
            $table->date('tracking_date');
            $table->string('source_location')->nullable();
            $table->string('destination_location')->nullable();
            // Note: Foreign key will be added after suppliers table is created
            $table->unsignedBigInteger('contractor_id')->nullable();
            $table->string('transport_method')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('manifest_number')->nullable();
            $table->enum('status', ['in_transit', 'delivered', 'disposed', 'recycled', 'returned'])->default('in_transit');
            $table->foreignId('tracked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'tracking_date']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_tracking_records');
    }
};
