<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotify extends Notification
{
    use Queueable;
    protected $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
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
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->data['user_id'],
            'title' => $this->data['subject'] ?? $this->data['title'] ?? '',
            'message' =>  $this->data['message'],
            'visit_url' => $this->data['visit_url'],
            'status' => $this->data['status'],
            'order_id' => $this->data['order_id'],
            'notify_type' => $this->data['notify_type'],
        ];
    }
}
