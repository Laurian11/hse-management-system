<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Clearing All Database Data ===');
        $this->command->info('');

        $driver = DB::connection()->getDriverName();
        $this->command->info("Database driver: {$driver}");

        // Handle foreign key checks based on driver
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Get all table names based on driver
        if ($driver === 'mysql') {
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;
            $tableNames = array_map(function($table) use ($tableKey) {
                return $table->$tableKey;
            }, $tables);
        } elseif ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");
            $tableNames = array_map(function($table) {
                return $table->name;
            }, $tables);
        } else {
            // For other drivers, try to get tables from Schema
            $tableNames = [];
            $allTables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            foreach ($allTables as $table) {
                $tableNames[] = $table;
            }
        }

        $deletedCount = 0;

        foreach ($tableNames as $tableName) {
            // Skip migrations table and system tables
            if (in_array($tableName, ['migrations', 'sqlite_sequence'])) {
                continue;
            }

            try {
                $count = DB::table($tableName)->count();
                if ($count > 0) {
                    if ($driver === 'sqlite') {
                        // SQLite doesn't support TRUNCATE, use DELETE
                        DB::table($tableName)->delete();
                        // Reset auto-increment
                        DB::statement("DELETE FROM sqlite_sequence WHERE name='{$tableName}'");
                    } else {
                        DB::table($tableName)->truncate();
                    }
                    $this->command->info("✓ Cleared table: {$tableName} ({$count} records)");
                    $deletedCount += $count;
                } else {
                    $this->command->info("  Table {$tableName} is already empty");
                }
            } catch (\Exception $e) {
                $this->command->warn("  Could not clear table {$tableName}: " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->command->info('');
        $this->command->info("=== Database Cleared ===");
        $this->command->info("Total records deleted: {$deletedCount}");
        $this->command->info('');
        $this->command->info('Note: The migrations table was preserved.');
        $this->command->info('You can now run: php artisan db:seed');
    }
}

