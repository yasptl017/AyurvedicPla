<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAllDataCommand extends Command
{
    protected $signature = 'migrate:all-data';

    protected $description = 'Command description';

    public function handle()
    {
        // 1. Allow unlimited memory for this script
        ini_set('memory_limit', '-1');
        $targetConn = 'mysql';
        $sourceConn = 'sqlsrv';

        // 1. Get the SPECIFIC Database Name from config
        $dbName = DB::connection($targetConn)->getDatabaseName();
        $this->info("Targeting specific MySQL database: [$dbName]");

        // 2. Strict Table Retrieval
        // explicitly select tables ONLY for this database schema to avoid getting other DB tables.
        $tablesRaw = DB::connection($targetConn)->select(
            "SELECT TABLE_NAME
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ?
             AND TABLE_TYPE = 'BASE TABLE'",
            [$dbName]
        );

        // Extract just the names into an array
        $tables = array_map(function ($t) {
            return $t->TABLE_NAME; // or $t->table_name depending on fetch mode
        }, $tablesRaw);

        // Filter out Laravel specific tables if they exist
        $ignored = ['migrations', 'failed_jobs', 'password_resets', 'personal_access_tokens', 'sessions', 'jobs'];
        // Note: We use in_array check because your tables are PascalCase,
        // but these might be lowercase.
        $tables = array_filter($tables, function ($t) use ($ignored) {
            return !in_array(strtolower($t), $ignored);
        });

        $this->info("Found " . count($tables) . " tables to migrate in $dbName.");

        // 3. Disable Foreign Keys
        DB::connection($targetConn)->statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $tableName) {

            // Safety: Check if table exists in source (SQL Server)
            if (!Schema::connection($sourceConn)->hasTable($tableName)) {
                $this->warn("Skipping [$tableName]: Not found in SQL Server.");
                continue;
            }

            $this->info("Migrating table: $tableName");

            // Clear destination
            DB::connection($targetConn)->table($tableName)->truncate();

            // 4. Data Transfer
            // Using query builder without 'orderBy' if no primary key prevents crashing
            $query = DB::connection($sourceConn)->table($tableName);

            // We check for a generic 'Id' or 'ID' or 'id' for chunking
            // Since your DB is PascalCase, your primary key might be 'Id' or 'UserId'
            $pk = $this->detectPrimaryKey($sourceConn, $tableName);

            if ($pk) {
                $query->orderBy($pk)->chunk(100, function ($rows) use ($tableName, $targetConn) {
                    $this->insertBatch($tableName, $rows, $targetConn);
                });
            } else {
                // If no PK found, just grab all (careful with RAM)
                $rows = $query->get();
                $this->insertBatch($tableName, $rows, $targetConn);
            }
        }

        // 5. Re-enable Foreign Keys
        DB::connection($targetConn)->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Migration Complete!");
    }

    /**
     * Helper to find a sortable column for chunking
     */
    private function detectPrimaryKey($conn, $table)
    {
        // 1. Try Laravel Schema (might be slow)
        // $indexes = Schema::connection($conn)->getIndexes($table); ...

        // 2. Simple Guessing for common PascalCase keys
        $candidates = ['Id', 'ID', 'id', $table . 'Id', 'UserId'];

        return array_find($candidates, fn($col) => Schema::connection($conn)->hasColumn($table, $col));

    }

    private function insertBatch($table, $rows, $targetConn)
    {
        // 1. Convert to array
        $data = [];
        foreach ($rows as $row) {
            $rowArray = (array)$row;

            // --- FIX 1: Handle Date Timeouts/Defaults ---
            // SQL Server '0001-01-01' crashes MySQL. fix it here.
            foreach ($rowArray as $key => $value) {
                // Check if it looks like a date and is too old for MySQL
                if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                    if (str_starts_with($value, '0001') || str_starts_with($value, '1753')) {
                        // Set to NULL or a valid MySQL min date
                        $rowArray[$key] = null; // or '1970-01-01 00:00:01'
                    }
                }
            }
            $data[] = $rowArray;
        }

        if (empty($data)) return;

        // --- FIX 2: Smaller Batches (Avoid "Packet Too Large" / Stuck) ---
        // Instead of inserting 1000 at once, we try 50 at a time.
        // If 50 fails, we try 1 by 1 to find the exact bad row.
        $chunkSize = 50;

        foreach (array_chunk($data, $chunkSize) as $smallChunk) {
            try {
                DB::connection($targetConn)->table($table)->insert($smallChunk);
            } catch (Exception $e) {
                $this->warn("Batch failed. Switching to single-row insert to find the error...");

                // If a batch fails, insert 1 by 1 to find the specific culprit
                foreach ($smallChunk as $singleRow) {
                    try {
                        DB::connection($targetConn)->table($table)->insert($singleRow);
                    } catch (Exception $innerEx) {
                        // PRINT THE EXACT ERROR
                        $this->error("------------------------------------------------");
                        $this->error("FAILED ROW in table [$table]:");
                        $this->error("Error Message: " . $innerEx->getMessage());

                        // Print the data causing the issue (useful for debugging)
                        $this->line(print_r($singleRow, true));

                        // Optional: Stop script so you can read the error
                        exit;
                    }
                }
            }
        }
    }
}
