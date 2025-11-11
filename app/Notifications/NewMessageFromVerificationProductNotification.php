<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageFromVerificationProductNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     protected $message;
     protected $product;
    public function __construct($message,$product)
    {
        $this->message=$message;
        $this->product=$product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
     public function toDatabase($notifiable)
    {
        return [
            "product_id" => $this->product->id,
            'feedback' => $this->message,
            'message' => "Votre produit" . $this->product->product_name . "à été rejeté veuillez consultez votre boite de notification pour avoir plus de details",
        ];
    }

}
