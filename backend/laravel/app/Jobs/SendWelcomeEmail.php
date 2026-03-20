<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public $user)
    {
    }

    public function handle()
    {
        \Log::info("Send email to: " . $this->user->email);
    }
}