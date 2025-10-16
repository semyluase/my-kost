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
            $table->boolean('is_cancel')->default(false)->after('is_change_room');
        });

        Schema::table('tr_deposites', function (Blueprint $table) {
            $table->boolean('is_cancel')->default(false)->after('is_checkout');
        });

        Schema::table('log_rents', function (Blueprint $table) {
            $table->boolean('is_cancel')->default(false)->after('is_check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_rents', function (Blueprint $table) {
            //
        });
    }
};
