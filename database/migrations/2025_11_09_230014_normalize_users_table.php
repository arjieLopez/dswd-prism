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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->constrained('designations')->after('last_name');
            $table->foreignId('office_id')->nullable()->constrained('offices')->after('designation_id');
            $table->foreignId('role_id')->nullable()->constrained('roles')->after('office_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['office_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['designation_id', 'office_id', 'role_id']);
        });
    }
};
