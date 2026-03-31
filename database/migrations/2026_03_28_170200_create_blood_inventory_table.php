<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('users')->onDelete('no action');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->integer('volume_ml')->default(0); // Available volume in milliliters
            $table->integer('units_available')->default(0); // Number of units available
            $table->date('last_updated')->nullable();
            $table->enum('storage_condition', ['fresh', 'refrigerated', 'frozen'])->default('refrigerated');
            $table->date('expiry_date')->nullable(); // For blood components
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_inventory');
    }
};
