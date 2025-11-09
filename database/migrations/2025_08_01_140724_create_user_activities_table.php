<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 50); // 'created_pr', 'updated_pr', 'uploaded_document'
            $table->text('description')->nullable();
            $table->string('pr_number', 20)->nullable(); // For PR-related activities
            $table->string('document_name', 100)->nullable(); // For upload activities
            $table->json('details')->nullable(); // Additional details
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
};
