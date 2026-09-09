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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('otp')->nullable()->after('phone_number');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_expires_at');
            $table->string('image', 100)->default('icon.png')->after('otp_verified_at');
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'otp',
                'otp_expires_at',
                'otp_verified_at',
                'image',
            ]);
        });
    }
};
