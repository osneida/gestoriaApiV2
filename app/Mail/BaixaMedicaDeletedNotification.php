<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaixaMedicaDeletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $file;
    public $worker;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file, $worker)
    {
        //
        $this->file = $file;
        $this->worker = $worker;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.files.baixa_medica.deleted')->subject("S'ha eliminat una baixa medica");
    }
}
