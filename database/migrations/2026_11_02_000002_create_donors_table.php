<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('donor_code')->unique();
            $table->string('nik', 20)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20);
            $table->string('blood_type', 5); // A, B, AB, O
            $table->string('rhesus', 2)->default('+'); // +, -
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth_date');
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('systolic')->nullable();
            $table->integer('diastolic')->nullable();
            $table->decimal('hemoglobin', 4, 1)->nullable();
            $table->date('last_donation_date')->nullable();
            $table->integer('total_donations')->default(0);
            $table->string('verification_status')->default('pending'); // pending, verified, suspended
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('donor_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->date('donation_date');
            $table->string('blood_type', 5);
            $table->string('rhesus', 2);
            $table->string('component_type')->default('WB'); // WB, PRC, TC, FFP
            $table->integer('volume_ml')->default(350);
            $table->string('blood_bag_code')->nullable();
            $table->decimal('hemoglobin', 4, 1)->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('screening_result')->default('passed'); // passed, failed
            $table->text('notes')->nullable();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_histories');
        Schema::dropIfExists('donors');
    }
};
