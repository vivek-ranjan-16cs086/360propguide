<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   */


  protected function schedule(Schedule $schedule): void
  {
		$schedule->command('youtube:fetch-videos')->hourly();
		$schedule->command('newsletter:send')->monthly();
		$schedule->command('fest:cron')->daily(); 
		$schedule->command('generate:sitemap')->hourly();
		$schedule->command('queue:work --stop-when-empty')->everyMinute();
	
  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
