<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AltaWorker extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $worker;
    public $contract;
    public $files;
    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $worker, $contract, $generatedPdf)
    {
        $this->company = $company;
        $this->worker = $worker;
        $this->contract = $contract;
        $this->pdf = $generatedPdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this
            ->view('emails.alta.alta')
            ->subject("Nou treballador donat d'alta");

        $mail->attachData(
            $this->pdf->output(),
            "formAlta.pdf"

        );

        $a = explode(".", $this->contract->document_identity_file_a);
        $type_file =  $a[count($a) - 1];
        //Log::debug($this->contract->document_identity_file_a);
        //Log::debug(Storage::disk('s3')->exists($this->contract->document_identity_file_a));
        if ($this->contract->document_identity_file_a !== null && $this->contract->document_identity_file_a !== 'null' && Storage::disk('s3')->exists($this->contract->document_identity_file_a)) {
            //Log::debug("DNI A");
            $mail->attachFromStorageDisk('s3', $this->contract->document_identity_file_a, "DNI 1.$type_file");
        }
        $b = explode(".", $this->contract->document_identity_file_b);
        $type_file =  $b[count($b) - 1];
        if ($this->contract->document_identity_file_b !== null && $this->contract->document_identity_file_b !== 'null' && Storage::disk('s3')->exists($this->contract->document_identity_file_b)) {
            $mail->attachFromStorageDisk('s3', $this->contract->document_identity_file_b, "DNI 2.$type_file");
        }
        $nss = explode(".", $this->contract->nss_file);
        $type_file =  $nss[count($nss) - 1];
        if ($this->contract->nss_file !== null && $this->contract->nss_file !== 'null' && Storage::disk('s3')->exists($this->contract->nss_file)) {
            $mail->attachFromStorageDisk('s3', $this->contract->nss_file, "nss.$type_file");
        }
        return $mail;
    }
}
