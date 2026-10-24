<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
	{
		if (Schema::hasColumn('projects', 'is_notified')) {
			return;
		}

		Schema::table('projects', function (Blueprint $table) {
			$table->boolean('is_notified')->default(false);
		});
	}

	public function down()
	{
		// This column may predate this migration in existing installations.
	}
};
