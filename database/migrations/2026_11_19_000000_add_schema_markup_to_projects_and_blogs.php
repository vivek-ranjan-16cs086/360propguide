<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['projects', 'blogs'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'schema_markup')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->longText('schema_markup')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['projects', 'blogs'] as $tableName) {
            if (Schema::hasColumn($tableName, 'schema_markup')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('schema_markup');
                });
            }
        }
    }
};
