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
        // Truncate long values (skip pr_number due to unique constraint and nulls)
        DB::table('user_activities')->whereRaw('LENGTH(action) > 50')->update(['action' => DB::raw('LEFT(action, 50)')]);
        DB::table('user_activities')->whereRaw('LENGTH(document_name) > 100')->update(['document_name' => DB::raw('LEFT(document_name, 100)')]);

        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('action', 50)->change();
            $table->text('description')->nullable()->change();
            $table->string('pr_number', 50)->nullable()->change(); // Increased to match purchase_requests
            $table->string('document_name', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('action', 255)->change();
            $table->string('description', 255)->change();
            $table->string('pr_number', 255)->change();
            $table->string('document_name', 255)->change();
        });
    }
};
