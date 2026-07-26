<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /** @var Migration $migration */
        $migration = require __DIR__.'/2026_07_26_101530_convert_domain_tables_to_uuid_v7.php';

        $migration->up();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('UUID v7 migration cannot be reversed without a database backup.');
    }
};
