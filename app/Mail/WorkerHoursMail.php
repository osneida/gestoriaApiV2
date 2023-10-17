<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkerHoursMail extends Mailable
{
    use Queueable, SerializesModels;

    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachments)
    {
        //
        $this->files = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.worker_hours');
        foreach ($this->files as $data) {
            $mail->attachData($data["file"], $data["name"] . "xlsx");
        }
        return $mail;
    }
}
