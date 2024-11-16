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
        Schema::create('tr_facility_h', function (Blueprint $table) {
            $table->id();
            $table->string('nobukti');
            $table->date('tanggal');
            $table->foreignId('room_id');
            $table->boolean('is_finish')->default(0);
            $table->foreignId('user_finish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_facility_h');
    }
};
