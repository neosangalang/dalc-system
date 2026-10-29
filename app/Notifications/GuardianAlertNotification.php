<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuardianAlertNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $url;
    public ?string $studentName;
    public ?string $actionText;
    public ?string $extraDetails;
    public bool $sendEmail;

    /**
     * Create a new notification instance.
     *
     * @param string $title Short category/headline, e.g. "Daily Report & Home Activity", "Quarterly Report Approved"
     * @param string $message Clear explanatory message for the guardian
     * @param string $url Link to the relevant portal page
     * @param string|null $studentName Child's name
     * @param string|null $actionText Button label (default: "View on Portal")
     * @param string|null $extraDetails Optional highlights (e.g., home reinforcement activity snippet)
     * @param bool $sendEmail Whether to send an email in addition to database notification (default: true)
     */
    public function __construct(
        string $title,
        string $message,
        string $url = '#',
        ?string $studentName = null,
        ?string $actionText = 'View on Portal',
        ?string $extraDetails = null,
        bool $sendEmail = true
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->studentName = $studentName;
        $this->actionText = $actionText ?: 'View on Portal';
        $this->extraDetails = $extraDetails;
        $this->sendEmail = $sendEmail;
    }

    /**
     * Get the notification's delivery channels.
     * Delivers to database bell, and if the user has a valid email and $sendEmail is true, sends email as well.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->sendEmail && !empty($notifiable->email) && filter_var($notifiable->email, FILTER_VALIDATE_EMAIL)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "[DALC] {$this->title}" . ($this->studentName ? " - {$this->studentName}" : '');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message);

        if (!empty($this->extraDetails)) {
            $mail->line($this->extraDetails);
        }

        $actionUrl = $this->url !== '#' ? url($this->url) : url('/guardian/dashboard');

        $mail->action($this->actionText, $actionUrl)
            ->line('Please sign in to the parent portal to review the updates and maintain continuity with your child\'s learning plan.')
            ->line('Thank you for partnering with Dream Achievers Learning Center PH!');

        return $mail;
    }

    /**
     * Get the array representation of the notification for the In-App Bell dropdown & history.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'        => $this->title,
            'message'      => $this->message,
            'url'          => $this->url,
            'student_name' => $this->studentName,
            'icon'         => 'fa-bell',
        ];
    }
}

