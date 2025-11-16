<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate long values to fit new lengths
        DB::table('users')->whereRaw('LENGTH(first_name) > 100')->update(['first_name' => DB::raw('LEFT(first_name, 100)')]);
        DB::table('users')->whereRaw('LENGTH(middle_name) > 100')->update(['middle_name' => DB::raw('LEFT(middle_name, 100)')]);
        DB::table('users')->whereRaw('LENGTH(last_name) > 100')->update(['last_name' => DB::raw('LEFT(last_name, 100)')]);
        DB::table('users')->whereRaw('LENGTH(designation) > 100')->update(['designation' => DB::raw('LEFT(designation, 100)')]);
        DB::table('users')->whereRaw('LENGTH(office) > 100')->update(['office' => DB::raw('LEFT(office, 100)')]);
        DB::table('users')->whereRaw('LENGTH(employee_id) > 20')->update(['employee_id' => DB::raw('LEFT(employee_id, 20)')]);
        DB::table('users')->whereRaw('LENGTH(role) > 20')->update(['role' => DB::raw('LEFT(role, 20)')]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->change();
            $table->string('middle_name', 100)->nullable()->change();
            $table->string('last_name', 100)->change();
            $table->string('designation', 100)->nullable()->change();
            $table->string('office', 100)->nullable()->change();
            $table->string('employee_id', 20)->nullable()->change();
            $table->string('role', 20)->default('user')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 255)->change();
            $table->string('middle_name', 255)->change();
            $table->string('last_name', 255)->change();
            $table->string('designation', 255)->change();
            $table->string('office', 255)->change();
            $table->string('employee_id', 255)->change();
            $table->string('role', 255)->change();
        });
    }
};
