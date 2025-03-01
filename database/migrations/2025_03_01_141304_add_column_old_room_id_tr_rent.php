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
            $table->foreignId('old_room_id')->nullable();
            $table->boolean('is_change_room')->default(false);
            $table->boolean('is_upgrade')->default(false);
            $table->boolean('is_downgrade')->default(false);
            $table->boolean('is_checkout_abnormal')->default(false);
            $table->boolean('is_checkout_normal')->default(false);
            $table->date('tanggal_transaksi')->nullable();
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
