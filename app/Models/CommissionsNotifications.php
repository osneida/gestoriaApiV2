<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Mail\Mailable;

class CommissionsNotifications extends Mailable
{
    use Queueable;

    public $commissions;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($commissions)
    {
        //
        $this->commissions = $commissions;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function build()
    {
        return $this->view('emails.no_iban')->subject("No s'ha informat del compte bancari dels treballadors");
    }
}
