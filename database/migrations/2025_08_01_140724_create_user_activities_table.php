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
            $table->string('action'); // 'created_pr', 'updated_pr', 'uploaded_document'
            $table->string('description');
            $table->string('pr_number')->nullable(); // For PR-related activities
            $table->string('document_name')->nullable(); // For upload activities
            $table->json('details')->nullable(); // Additional details
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
};
