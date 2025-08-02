<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('po_number')->nullable();
            $table->timestamp('po_generated_at')->nullable();
            $table->string('po_generated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['po_number', 'po_generated_at', 'po_generated_by']);
        });
    }
};
