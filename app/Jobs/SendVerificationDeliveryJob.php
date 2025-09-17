<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Seller\NewMessageFromVerificationNotification;

class SendVerificationDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     protected $userId;
     protected $message;
    public function __construct($userId,$message)
    {
        $this->userId=$userId;
        $this->message=$message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user=User::find($this->userId);
        $user->notify(new NewMessageFromVerificationNotification($this->message));
    }
}
