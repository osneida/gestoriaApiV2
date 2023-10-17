<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoIbanNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $company;
    public $workers;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $workers)
    {
        //
        $this->company = $company;
        $this->workers = $workers;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.no_iban')->subject("No s'ha informat del compte bancari dels treballadors");
    }
}
