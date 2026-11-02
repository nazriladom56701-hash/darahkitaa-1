<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('blood_type', 5);
            $table->string('rhesus', 2)->default('+');
            $table->string('component', 10)->default('WB'); // WB, PRC, TC, FFP
            $table->integer('total_units')->default(0);
            $table->integer('min_threshold')->default(10);
            $table->timestamps();

            $table->unique(['blood_type', 'rhesus', 'component']);
        });

        Schema::create('blood_bags', function (Blueprint $table) {
            $table->id();
            $table->string('bag_code')->unique();
            $table->string('blood_type', 5);
            $table->string('rhesus', 2)->default('+');
            $table->string('component', 10)->default('WB');
            $table->integer('volume_ml')->default(350);
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('donor_events')->nullOnDelete();
            $table->date('collection_date');
            $table->date('expiry_date');
            $table->string('storage_location')->default('Chamber A - Rak 1');
            $table->string('status')->default('available'); // available, allocated, transfused, expired, discarded
            $table->timestamps();
        });

        Schema::create('blood_stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('blood_type', 5);
            $table->string('rhesus', 2)->default('+');
            $table->string('component', 10)->default('WB');
            $table->enum('type', ['in', 'out', 'expired', 'adjustment']);
            $table->integer('quantity'); // positive for in, negative for out
            $table->string('bag_code')->nullable();
            $table->string('reference_type')->nullable(); // donation, hospital_request, disposal, stock_opname
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_stock_mutations');
        Schema::dropIfExists('blood_bags');
        Schema::dropIfExists('blood_stocks');
    }
};

