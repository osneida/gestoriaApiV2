<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyWorkerUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $worker;
    public $camp;
    public $value;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($worker, $camp, $value)
    {
        //
        $this->worker = $worker;
        $this->camp = $camp;
        $this->value = $value;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Actualitzacio de treballador")->view('emails.workers.my_worker_update');
    }
}
