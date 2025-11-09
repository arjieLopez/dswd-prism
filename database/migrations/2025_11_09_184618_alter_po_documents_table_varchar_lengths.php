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
        // Truncate long values
        DB::table('po_documents')->whereRaw('LENGTH(file_name) > 100')->update(['file_name' => DB::raw('LEFT(file_name, 100)')]);
        DB::table('po_documents')->whereRaw('LENGTH(file_type) > 50')->update(['file_type' => DB::raw('LEFT(file_type, 50)')]);
        DB::table('po_documents')->whereRaw('LENGTH(file_size) > 20')->update(['file_size' => DB::raw('LEFT(file_size, 20)')]);

        Schema::table('po_documents', function (Blueprint $table) {
            $table->string('po_number', 50)->change(); // Increased to accommodate existing data
            $table->string('file_name', 100)->change();
            $table->string('file_path', 255)->change();
            $table->string('file_type', 50)->change();
            $table->string('file_size', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('po_documents', function (Blueprint $table) {
            $table->string('po_number', 255)->change();
            $table->string('file_name', 255)->change();
            $table->string('file_path', 255)->change();
            $table->string('file_type', 255)->change();
            $table->string('file_size', 255)->change();
        });
    }
};
