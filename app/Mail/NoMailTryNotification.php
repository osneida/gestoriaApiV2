<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoMailTryNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $worker;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($worker)
    {
        $this->worker = $worker;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.try_send_payrolls')->subject("No s'ha pogut enviar la nòmina amb 5 intents");
    }
}
