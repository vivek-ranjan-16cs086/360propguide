<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('cities');
            $table->unsignedBigInteger('sublocation_id')->nullable()->after('location_id');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->nullOnDelete();

            $table->foreign('sublocation_id')
                ->references('id')
                ->on('locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['sublocation_id']);
            $table->dropColumn(['location_id', 'sublocation_id']);
        });
    }
};
