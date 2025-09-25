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
        Schema::table('tr_transaction_d', function (Blueprint $table) {
            if (Schema::hasColumn('tr_deposites', 'is_express')) {
                $table->dropColumn('is_express');
            }

            if (Schema::hasColumn('tr_deposites', 'category_laundry_id')) {
                $table->dropColumn('category_laundry_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_transaction_d', function (Blueprint $table) {
            //
        });
    }
};
