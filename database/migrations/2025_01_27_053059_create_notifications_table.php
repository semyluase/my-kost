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
        Schema::create('tr_notification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('for_role')->nullable();
            $table->foreignId('for_user')->nullable();
            $table->string('modul')->nullable();
            $table->string('type_transaction')->nullable();
            $table->string('no_transaction')->nullable();
            $table->boolean('is_read')->default(false);
            $table->foreignId('by_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_notification');
    }
};
