<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_type')->default('donor'); // donor, hospital, officer
            $table->string('recipient_name');
            $table->string('recipient_contact');
            $table->string('type'); // donor_recall, event_invitation, critical_stock, request_status
            $table->string('title');
            $table->text('message');
            $table->string('channel')->default('whatsapp'); // whatsapp, email, sms
            $table->string('status')->default('sent'); // sent, pending, failed
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_notifications');
    }
};
