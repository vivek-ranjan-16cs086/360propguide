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
        Schema::create('custom_links', function (Blueprint $table) {
            $table->id();
			$table->string('name')->nullable(); 
			$table->string('title')->nullable(); 
			$table->string('canonical')->nullable(); 
			$table->text('keywords')->nullable(); 
            $table->string('slug')->unique();    
            $table->text('description')->nullable(); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_links');
    }
};
