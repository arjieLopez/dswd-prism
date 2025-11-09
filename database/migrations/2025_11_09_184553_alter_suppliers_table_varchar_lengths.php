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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_name', 100)->change();
            $table->string('tin', 20)->change();
            $table->string('contact_person', 100)->change();
            $table->string('contact_number', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_name', 255)->change();
            $table->string('tin', 255)->change();
            $table->string('contact_person', 255)->change();
            $table->string('contact_number', 255)->change();
        });
    }
};
