<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoMailNotification extends Mailable
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
        return $this->view('emails.no_email')->subject("No s'ha informat del correu electronic del treballador dels treballadors");
    }
}
