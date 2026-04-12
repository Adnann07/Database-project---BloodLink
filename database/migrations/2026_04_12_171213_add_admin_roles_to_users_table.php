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
        // First, if using ENUM, modifying it can be complex across different DBMS.
        // For MySQL, we can use DB statement or Doctrine DBAL.
        // It's safest to use DB::statement for enum changes on base tables.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'donor', 'hospital') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('donor', 'hospital') NOT NULL");
    }
};
