<?php

namespace App\Mail;

use App\Models\DailyLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DailyLog $log) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Daily Progress Report – {$this->log->student->first_name} – {$this->log->log_date->format('M d, Y')}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report',
        );
    }
}