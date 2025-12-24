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
        Schema::table('konter', function (Blueprint $table) {
            $table->dropIndex('konter_type_category_unique');
            $table->unique(['type', 'category', 'home_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konter', function (Blueprint $table) {
            //
        });
    }
};
