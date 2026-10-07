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
        Schema::create('wedding_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('title')->default('শুভ বিবাহ');
            $table->string('bengali_date')->nullable();
            $table->string('tithi')->nullable(); // e.g. রাত্রিলগ্ন, দিবালগ্ন, অমৃতযোগ
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_dates');
    }
};
