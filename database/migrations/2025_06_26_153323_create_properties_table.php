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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->string('city')->nullable();
            $table->string('property_type')->nullable();
            $table->string('listing_type')->nullable();
            $table->string('configuration')->nullable();//
            $table->string('construction_status')->nullable();
            $table->double('total_price')->nullable();
            $table->double('area')->nullable();//
            $table->string('area_unit')->nullable();
            $table->string('furnishing_types')->nullable();//
            $table->text('title')->nullable();
            $table->text('slug')->unique()->nullable();
            $table->longText('seo_data')->nullable();
            $table->longText('property_details')->nullable();//
            $table->longText('advanced_details')->nullable();//
            $table->longText('price_details')->nullable();
            $table->longText('amenities')->nullable();
            $table->longText('galleries')->nullable();
            $table->string('is_verified')->nullable();
            $table->string('active')->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('step_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
