<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscriber;
use App\Mail\Newsletter;
use Illuminate\Support\Facades\Mail;

class SendNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:send-newsletter';
	protected $signature = 'fest:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $subscribers = Subscriber::all();

        // foreach ($subscribers as $subscriber) {
            // Mail::to($subscriber->email)->send(new Newsletter());
        // }
		\Log::info("Cron is working fine!");
    }
}
