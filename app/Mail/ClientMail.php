<?php

namespace App\Mail;

use App\Models\Client;
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


    /**
     * Create a new message instance.
     */
    public function __construct( Client $client)
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
                'activation_token' => $this->client->activation_token
                
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
