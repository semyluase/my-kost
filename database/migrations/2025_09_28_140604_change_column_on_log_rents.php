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
        Schema::table('log_rents', function (Blueprint $table) {
            $table->boolean('is_check_in')->default(false)->change();
            $table->boolean('is_check_out')->default(false)->change();
            $table->boolean('is_upgrade')->default(false)->change();
            $table->boolean('is_downgrade')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_rents', function (Blueprint $table) {
            //
        });
    }
};
