<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMailDescription extends Mailable
{
    use Queueable, SerializesModels;
    public $ticket;
    public $workDescription;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $workDescription)
    {
        $this->ticket = $ticket;
        $this->workDescription = $workDescription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Evolution de votre ticket',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email',
            with:[
                'ticket'=>$this->ticket,
                'workDescription'=> $this->workDescription,
            ]
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
