<?php

namespace App\Mail;

use App\Models\Historique;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $ticket;
    public $report;
    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, Historique $report)
    {
        $this->ticket = $ticket;
        $this->report = $report;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rapport de votre ticket',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_report_email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('emails.ticket_report', ['ticket'=>$this->ticket, 'report' => $this->report]);
        return [
            Attachment::fromData(fn ()=> $pdf->output(), 'ticket_report.pdf')
            ->withMime('application/pdf'),
        ];
    }
}
