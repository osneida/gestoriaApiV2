<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractExpires extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $message;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     */
    public function __construct(string $subject, string $message){
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $markdown = $this->markdown('emails.contract_expires');
        $markdown->subject($this->subject);
        return $markdown;
    }
}
