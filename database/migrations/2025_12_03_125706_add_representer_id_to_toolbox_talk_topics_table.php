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
        Schema::table('toolbox_talk_topics', function (Blueprint $table) {
            $table->foreignId('representer_id')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('toolbox_talk_topics', function (Blueprint $table) {
            $table->dropForeign(['representer_id']);
            $table->dropColumn('representer_id');
        });
    }
};
