<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalariesNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $salaries;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($salaries)
    {
        //
        $this->salaries = $salaries;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.salaries')->subject("Salaris");
    }
}
