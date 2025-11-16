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
        Schema::table('system_selections', function (Blueprint $table) {
            $table->string('type', 50)->change();
            $table->string('name', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('system_selections', function (Blueprint $table) {
            $table->string('type', 255)->change();
            $table->string('name', 255)->change();
        });
    }
};
