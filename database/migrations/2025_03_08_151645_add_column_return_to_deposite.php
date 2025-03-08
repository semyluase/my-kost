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
        Schema::table('tr_deposites', function (Blueprint $table) {
            $table->bigInteger('pengembalian', false, true);
            $table->date('tanggal');
            $table->boolean('is_returned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_deposites', function (Blueprint $table) {
            //
        });
    }
};
