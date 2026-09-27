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
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->date('event_date');
            $table->string('slot'); // morning, evening, full_day
            $table->string('event_type')->nullable(); // Wedding, Holud, etc.
            $table->integer('guest_count')->default(0);
            $table->integer('table_count')->default(0);
            $table->integer('server_count')->default(0);
            $table->boolean('is_ac')->default(false);
            $table->boolean('extra_decoration')->default(false);
            $table->boolean('extra_sound')->default(false);
            $table->boolean('extra_generator')->default(false);
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
