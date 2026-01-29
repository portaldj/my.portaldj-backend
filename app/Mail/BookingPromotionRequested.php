<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingPromotionRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $event;
    public $user;
    public $pressRelease;

    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, User $user, string $pressRelease)
    {
        $this->event = $event;
        $this->user = $user;
        $this->pressRelease = $pressRelease;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Promotion Request from ' . $this->user->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking_promotion',
            with: [
                'eventTitle' => $this->event->title,
                'eventDate' => $this->event->start->format('d/m/Y H:i'),
                'pressRelease' => $this->pressRelease,
                'djName' => $this->user->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
