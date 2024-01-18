<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

use App\Models\ext_guarantee;
use App\Models\ext_customer;

class GarantiaExtendida extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $ext_guarantee;
    public $ext_customer;

    public function __construct(ext_guarantee $ext_guarantee, ext_customer $ext_customer)
    {
        $this->ext_guarantee = $ext_guarantee;
        $this->ext_customer = $ext_customer;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        
        return new Envelope(
            from: new Address('rc@ynk.cl', 'AUFBAU GARANTIA EXTENDIDA'),
            replyTo: [
                new Address('no-responder@aufbau.cl', 'No Responder'),
            ],
            subject: 'Garantia Extendida',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        //return view('gext', ['extguarantees' => $this->extguarantees]);
        return new Content(
            view: 'gext'
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
