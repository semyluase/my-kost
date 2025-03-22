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
            $table->string('jenis_pengembalian');
            $table->foreignId('bank')->nullable()->change();
            $table->string('no_rek')->nullable()->change();
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
