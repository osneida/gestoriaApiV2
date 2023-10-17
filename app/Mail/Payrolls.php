<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Payrolls extends Mailable
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
     * @param string $subject
     * @param string $message
     * @param array $data
     */
    public function __construct(string $subject, string $message, array $data, bool $adjuntar)
    {
        Log::info('Rastro 7');
        $this->data = $data;
        $this->subject = $subject;
        $this->msg = $message;
        $this->adjuntar = $adjuntar;
        Log::info('Rastro 8');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('Rastro 1');
        $markdown = $this->view('emails.payrolls');
        Log::info('Rastro 2');
        $markdown->subject($this->subject);
        Log::info('Rastro 3', ['adjuntar' => $this->adjuntar]);
        foreach ($this->data as $data) {
            if ($data["s3_path"]) {
                Log::info('Rastro 4');
                
                Log::info('Rastro 5');
                $markdown->attachFromStorageDisk("s3", $data["s3_path"], $data["period"]);
                Log::info('Rastro 6');
                Log::info('mensaje de UBICACION 2',['data'=>$markdown]);
            }
        }
        return $markdown;
    }
}
