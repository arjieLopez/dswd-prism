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
        Schema::table('uploaded_documents', function (Blueprint $table) {
            $table->string('pr_number', 20)->change();
            $table->string('file_path', 255)->change();
            $table->string('original_filename', 255)->change();
            $table->string('file_type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('uploaded_documents', function (Blueprint $table) {
            $table->string('pr_number', 255)->change();
            $table->string('file_path', 255)->change();
            $table->string('original_filename', 255)->change();
            $table->string('file_type', 255)->change();
        });
    }
};
