<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate long values if any
        DB::table('common_attributes')->whereRaw('LENGTH(entity_type) > 50')->update(['entity_type' => DB::raw('LEFT(entity_type, 50)')]);
        DB::table('common_attributes')->whereRaw('LENGTH(attribute_key) > 50')->update(['attribute_key' => DB::raw('LEFT(attribute_key, 50)')]);

        Schema::table('common_attributes', function (Blueprint $table) {
            $table->string('entity_type', 50)->change();
            $table->string('attribute_key', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('common_attributes', function (Blueprint $table) {
            $table->string('entity_type', 255)->change();
            $table->string('attribute_key', 255)->change();
        });
    }
};
