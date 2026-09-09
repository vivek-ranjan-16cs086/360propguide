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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('position')->nullable(); // Job position
            $table->integer('experience')->nullable(); // Required experience in years
            $table->integer('open_positions')->nullable(); // Number of open positions
            $table->string('location')->nullable(); // Job location
            $table->string('company_logo')->nullable(); // Path to the company logo (optional)
            $table->text('job_description')->nullable(); // Detailed job description
            $table->date('date_posted')->nullable(); // Date the job was posted
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
