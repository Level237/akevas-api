<?php

namespace App\Notifications\Seller;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class NewOrderFromSellerNotification extends Notification
{
    use Queueable;

    protected $order;
    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
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

     public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->userName,
            'total_amount' => $this->order->total,
            'message' => 'Vous aviez recu une nouvelle commande: #' . $this->order->id,
        ];
    }

    
    /**
     * Get the mail representation of the notification.
     */
    
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}
