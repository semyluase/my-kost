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
        Schema::create('tr_facility_d', function (Blueprint $table) {
            $table->id();
            $table->string('nobukti');
            $table->foreignId('order_facility_id');
            $table->double('qty');
            $table->string('oum');
            $table->double('total');
            $table->boolean('is_express')->default(0);
            $table->timestamp('finish_estimate')->nullable();
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
        Schema::dropIfExists('tr_facility_d');
    }
};
