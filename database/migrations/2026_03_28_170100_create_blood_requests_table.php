<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('users')->onDelete('no action');
            $table->string('request_id')->unique(); // Unique request identifier
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->integer('volume_ml')->nullable();
            $table->text('patient_details')->nullable();
            $table->text('medical_notes')->nullable();
            $table->date('required_date');
            $table->time('required_time')->nullable();
            $table->enum('status', ['pending', 'matched', 'fulfilled', 'cancelled', 'expired'])->default('pending');
            $table->foreignId('matched_donor_id')->nullable()->constrained('users')->onDelete('no action');
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
