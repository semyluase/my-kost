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
        Schema::table('tr_rent', function (Blueprint $table) {
            $table->integer('total_sewa')->default(0);
            $table->bigInteger('total_harga_sewa')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_rent', function (Blueprint $table) {
            //
        });
    }
};
