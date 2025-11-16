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
        Schema::create('system_selections', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_selections');
    }
};
