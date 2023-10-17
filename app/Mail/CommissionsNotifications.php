<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommissionsNotifications extends Mailable
{
    use Queueable, SerializesModels;
    public $commisions;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($commisions)
    {
        //
        $this->commisions = $commisions;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.commisions')->subject("Commisions");
    }
}
