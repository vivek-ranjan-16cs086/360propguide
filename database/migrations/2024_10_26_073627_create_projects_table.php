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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->longText('hero_images')->nullable();//
            $table->text('about_description')->nullable();//
            $table->text('key_insights')->nullable();//
            $table->text('rera_no')->nullable();//
            $table->string('launch_date')->nullable();//
            $table->string('developer_name')->nullable();//
            $table->string('property_size')->nullable();//
            $table->string('typology')->nullable();//
            $table->string('project_status')->nullable();//
            $table->string('location')->nullable();//
            $table->text('location_video')->nullable();//
            $table->string('location_description')->nullable();//
            $table->text('floor_plans_images')->nullable();//
            $table->text('floor_plans_description')->nullable();//
            $table->text('site_plans_images')->nullable();//
            $table->text('site_plans_description')->nullable();//
            $table->text('possession_description')->nullable();//
            $table->string('price')->nullable();//
            $table->text('price_list')->nullable();//
            $table->text('seo_data')->nullable();
            $table->text('amenities_images')->nullable();//
            $table->text('amenities_description')->nullable();//
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
