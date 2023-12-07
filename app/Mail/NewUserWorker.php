<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWorker extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $user;
    public $mod;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password, $mod = false)
    {
        //
        $this->user = $user;
        $this->password = $password;
        $this->mod = $mod;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Usuari acces portal treballador")->view('emails.users.new_worker');
    }
}
