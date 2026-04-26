<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $documentType; // e.g., 'Daily Report', 'Q1 Report', 'IEP'
    protected $studentName;
    protected $url;

    public function __construct($documentType, $studentName, $url)
    {
        $this->documentType = $documentType;
        $this->studentName = $studentName;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     * We tell Laravel to send BOTH an in-app alert AND an email!
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; 
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->documentType} Ready for Review")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new {$this->documentType} for {$this->studentName} has been generated and is ready for your review.")
            ->action('View Document', url($this->url))
            ->line('Thank you for partnering with Dream Achievers Learning Center!')
            ->theme('default');
    }

    /**
     * Get the array representation of the notification for the In-App Bell Icon.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New {$this->documentType}",
            'message' => "{$this->studentName}'s {$this->documentType} is ready for review.",
            'icon' => 'fa-file-signature',
            'url' => $this->url
        ];
    }
}