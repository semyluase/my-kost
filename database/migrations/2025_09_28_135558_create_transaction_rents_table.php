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
        Schema::create('log_rents', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->foreignId('room_id');
            $table->boolean('is_check_in');
            $table->boolean('is_check_out');
            $table->boolean('is_upgrade');
            $table->boolean('is_downgrade');
            $table->bigInteger('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_rents');
    }
};
