<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('logo_image')->nullable()->after('seo_data');
            $table->string('feature_image')->nullable()->after('logo_image');
            $table->text('youtube_links')->nullable()->after('feature_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['logo_image', 'feature_image', 'youtube_links']);
        });
    }
};
