<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Certificates extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $msg;
    public $adjuntar;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $message, array $data, bool $adjuntar)
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->msg = $message;
        $this->adjuntar = $adjuntar;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $markdown = $this->view('emails.certificates');
        $markdown->subject($this->subject);
        if ($this->adjuntar) {
            foreach ($this->data as $data) {
                $markdown->attachFromStorageDisk("s3", $data["s3_path"], $data["period"]);
            }
        }
        return $markdown;
    }
}
