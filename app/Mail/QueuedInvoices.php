<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

class QueuedInvoices extends Mailable
{
    use Queueable, SerializesModels;

    public $server;
    public $quantity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($server, $quantity)
    {
        $this->server = $server;
        $this->quantity = $quantity;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('intranet@ynk.cl', 'Intranet YNK'),
            subject: '[Monitor] Integración Documentos POS->SAP ' . $this->server . ' ' . Carbon::now()->format('d/m/Y H:i:s'),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.monitors.queued_invoices',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
