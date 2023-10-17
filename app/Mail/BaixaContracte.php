<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaixaContracte extends Mailable
{
    use Queueable, SerializesModels;

    public $contract;
    public $worker;
    public $company;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contract, $worker, $company, $type)
    {
        $this->contract = $contract;
        $this->worker = $worker;
        $this->company = $company;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {

        if ($this->type === 1)
            $mail =
                $this->view('emails.baixes.voluntaria')
                ->subject('Baixa voluntaria de treballador')
                ->attachFromStorageDisk(
                    's3',
                    $this->contract->baixa_voluntaria_file,
                    'carta_baixa.pdf'
                );

        else
            $mail =
                $this->view('emails.baixes.acomiadament')
                ->subject('Acomiadament de treballador');

        return $mail;
    }
}
