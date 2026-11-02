<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_code')->unique();
            $table->string('title');
            $table->string('organizer');
            $table->string('location');
            $table->text('address')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('quota')->default(100);
            $table->integer('target_bags')->default(80);
            $table->string('status')->default('upcoming'); // upcoming, ongoing, completed, cancelled
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('donor_events')->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->string('registration_code')->unique();
            $table->string('attendance_status')->default('registered'); // registered, present, absent
            $table->string('screening_status')->default('pending'); // pending, passed, failed
            $table->string('blood_pressure')->nullable();
            $table->decimal('hemoglobin', 4, 1)->nullable();
            $table->string('blood_bag_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['event_id', 'donor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
        Schema::dropIfExists('donor_events');
    }
};

