<?php

namespace App\Jobs;

use App\Mail\ContactMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Log;

class SendContactEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mailData;
    public $email;

    /**
     * Create a new job instance.
     */
    public function __construct($mailData, $email)
    {
        $this->mailData = $mailData;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		 \Log::info('SendContactEmailJob started');
        try {
			 \Log::info('Before sending mail to: ' . $this->email);
            Mail::to($this->email)->send(new ContactMail($this->mailData));
			 \Log::info('Mail sent successfully to: ' . $this->email);
        } catch (\Exception $e) {
            Log::error("Queued Mail Failed: " . $e->getMessage());
        }
    }
}