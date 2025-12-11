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
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        // Helper function to safely add index
        $addIndex = function ($table, $columns, $indexName = null) use ($connection, $driver) {
            if ($indexName === null) {
                $indexName = $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns) . '_index';
            }

            // Check if index already exists
            $exists = false;
            if ($driver === 'sqlite') {
                $result = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=? AND name=?", [$table, $indexName]);
                $exists = count($result) > 0;
            } else {
                try {
                    $result = $connection->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
                    $exists = count($result) > 0;
                } catch (\Exception $e) {
                    $exists = false;
                }
            }

            if (!$exists) {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    if (is_array($columns)) {
                        $table->index($columns, $indexName);
                    } else {
                        $table->index($columns, $indexName);
                    }
                });
            }
        };

        // Add indexes to incidents table
        $addIndex('incidents', 'company_id');
        $addIndex('incidents', 'status');
        $addIndex('incidents', 'severity');
        $addIndex('incidents', 'incident_date');
        $addIndex('incidents', ['company_id', 'status'], 'incidents_company_id_status_index');
        $addIndex('incidents', ['company_id', 'severity'], 'incidents_company_id_severity_index');

        // Add indexes to toolbox_talks table
        $addIndex('toolbox_talks', 'company_id');
        $addIndex('toolbox_talks', 'status');
        $addIndex('toolbox_talks', 'scheduled_date');
        $addIndex('toolbox_talks', ['company_id', 'status'], 'toolbox_talks_company_id_status_index');
        $addIndex('toolbox_talks', ['company_id', 'scheduled_date'], 'toolbox_talks_company_id_scheduled_date_index');

        // Add indexes to toolbox_talk_attendances table
        $addIndex('toolbox_talk_attendances', 'toolbox_talk_id');
        $addIndex('toolbox_talk_attendances', 'employee_id');
        $addIndex('toolbox_talk_attendances', 'attendance_status');
        $addIndex('toolbox_talk_attendances', 'check_in_time');
        $addIndex('toolbox_talk_attendances', ['toolbox_talk_id', 'attendance_status'], 'toolbox_talk_attendances_toolbox_talk_id_attendance_status_index');

        // Add indexes to users table (only if they don't exist)
        $addIndex('users', 'company_id');
        $addIndex('users', 'department_id');
        // Skip role_id if it already exists (from foreign key)
        try {
            $addIndex('users', 'role_id');
        } catch (\Exception $e) {
            // Index may already exist from foreign key
        }
        $addIndex('users', 'is_active');
        $addIndex('users', ['company_id', 'is_active'], 'users_company_id_is_active_index');

        // Add indexes to safety_communications table
        $addIndex('safety_communications', 'company_id');
        $addIndex('safety_communications', 'status');
        $addIndex('safety_communications', 'priority_level'); // Note: column is named priority_level, not priority
        $addIndex('safety_communications', 'scheduled_send_time'); // Note: column is named scheduled_send_time, not scheduled_send_at
        $addIndex('safety_communications', ['company_id', 'status'], 'safety_communications_company_id_status_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['severity']);
            $table->dropIndex(['incident_date']);
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['company_id', 'severity']);
        });

        Schema::table('toolbox_talks', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['scheduled_date']);
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['company_id', 'scheduled_date']);
        });

        Schema::table('toolbox_talk_attendances', function (Blueprint $table) {
            $table->dropIndex(['toolbox_talk_id']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['attendance_status']);
            $table->dropIndex(['check_in_time']);
            $table->dropIndex(['toolbox_talk_id', 'attendance_status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['department_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['company_id', 'is_active']);
        });

        Schema::table('safety_communications', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['priority_level']); // Note: column is named priority_level, not priority
            $table->dropIndex(['scheduled_send_time']); // Note: column is named scheduled_send_time, not scheduled_send_at
            $table->dropIndex(['company_id', 'status']);
        });
    }
};

