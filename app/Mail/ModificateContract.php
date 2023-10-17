<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ModificateContract extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $worker;
    public $contract;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $worker, $contract)
    {
        //
        $this->company = $company;
        $this->worker = $worker;
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Modificació de contracte")->view('emails.modificacions.modificacions');
    }
}
