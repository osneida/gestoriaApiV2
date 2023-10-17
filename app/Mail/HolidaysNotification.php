<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HolidaysNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $approved;
    public $worker;
    public $start_date;
    public $end_date;
    public $approver;
    public $anulated;
    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->approved === null) {
            $mail = $this->view('emails.holidays_request')->subject("Solicitud de vacances");
        } else if ($this->approved) {
            $mail = $this->view('emails.holidays_approve')->subject("Vacances aprovades");
        } else if (!$this->anulated) {
            $mail = $this->view('emails.holidays_reject')->subject("Vacances rebutjades");
        } else {
            $mail = $this->view('emails.holidays_anulated')->subject("Vacances anulades");
        }
        return $mail;
    }
}
