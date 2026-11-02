<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique();
            $table->string('hospital_name');
            $table->string('doctor_in_charge');
            $table->string('contact_phone');
            $table->string('patient_name');
            $table->string('medical_record_number');
            $table->string('blood_type', 5);
            $table->string('rhesus', 2)->default('+');
            $table->string('component', 10)->default('WB'); // WB, PRC, TC, FFP
            $table->integer('units_requested');
            $table->enum('urgency', ['normal', 'urgent', 'emergency'])->default('normal');
            $table->text('diagnosis_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('action_at')->nullable();
            $table->string('handover_recipient')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_requests');
    }
};

