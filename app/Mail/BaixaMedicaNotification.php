<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BaixaMedicaNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $worker;
    public $type;
    public $file;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $worker, $type, $file)
    {
        //
        $this->company = $company;
        $this->worker = $worker;
        $this->type = $type;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.files.baixa_medica.new')->subject(ucfirst($this->type) . " mèdica");
        $end = explode(".", $this->file);
        //Log::debug($end);
        $end = $end[count($end) - 1];
        //Log::debug($end);
        $mail->attachFromStorageDisk(
            's3',
            $this->file,
            "$this->type.$end"
        );
        return $mail;
    }
}
