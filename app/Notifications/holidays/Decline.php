<?php

namespace App\Notifications\holidays;

use App\Mail\HolidaysNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Decline extends Notification
{
    use Queueable;

    public $approved;
    public $worker;
    public $start_date;
    public $end_date;
    public $approver;
    public $anulated;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($approved, $worker, $start_date, $end_date, $approver, $anulated)
    {
        $this->approved = $approved;
        $this->worker = $worker;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->approver = $approver;
        $this->anulated = $anulated;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', "database"];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new HolidaysNotification($this->approved, $this->worker, $this->start_date, $this->end_date, $this->approver, $this->anulated))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
