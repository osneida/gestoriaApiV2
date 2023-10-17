<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NoPayrollNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $period;
    public $company;
    private $months = ["Gener", "Febrer", "Març", "Abril", "Maig", "Juny", "Juliol", "Agost", "Setembre", "Octubre", "Novembre", "Desembre"];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $period)
    {
        $this->company = $company;
        setlocale(LC_TIME, "");
        $fecha = date_create_from_format('Y-m', $period);
        $month = (int) $fecha->format("m");
        $this->period = $this->months[$month - 1];
        //Log::info($this->period);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.no_payrolls');
    }
}
