<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($client)
    {
        $this->client = $client;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activation de compte',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'pageEmail',
            with: [
                'nom_clt' => $this->client->nom_clt,
                // 'url' => url(`http://127.0.0.1:8080/reset-password`),
            ]
        );
    }

    // public function build()
    // {
    //     return $this->view('pageEmail')
    //         ->with([
    //             'nom_clt' => $this->client->nom_clt,
    //             'url' => $this->url,
    //         ]);
    // }


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
