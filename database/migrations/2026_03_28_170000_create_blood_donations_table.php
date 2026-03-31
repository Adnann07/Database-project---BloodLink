<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->onDelete('no action');
            $table->foreignId('hospital_id')->nullable()->constrained('users')->onDelete('no action');
            $table->string('donation_type')->default('regular');
            $table->date('donation_date');
            $table->time('donation_time');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'pending'])->default('scheduled');
            $table->integer('volume_ml')->nullable();
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_donations');
    }
};
