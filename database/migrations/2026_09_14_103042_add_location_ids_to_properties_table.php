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
        $addLocationId = !Schema::hasColumn('properties', 'location_id');
        $addSublocationId = !Schema::hasColumn('properties', 'sublocation_id');

        if (!$addLocationId && !$addSublocationId) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable();
            }
            if (!Schema::hasColumn('properties', 'sublocation_id')) {
                $table->unsignedBigInteger('sublocation_id')->nullable();
            }
        });

        Schema::table('properties', function (Blueprint $table) use ($addLocationId, $addSublocationId) {
            if ($addLocationId) {
                $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->nullOnDelete();
            }
            if ($addSublocationId) {
                $table->foreign('sublocation_id')
                    ->references('id')
                    ->on('locations')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These columns may predate this migration in existing installations.
    }
};
