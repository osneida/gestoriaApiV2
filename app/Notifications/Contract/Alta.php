<?php

namespace App\Notifications\Contract;

use App\Mail\AltaWorker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Alta extends Notification
{
    use Queueable;

    public $contract;
    public $worker;
    public $file;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($contract, $worker, $file)
    {
        $this->contract = $contract;
        $this->worker = $worker;
        $this->file = $file;
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
        return (new AltaWorker(
            $this->contract->company,
            $this->worker,
            $this->contract,
            $this->file
        ))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $editor = '-';
        $lastModification = $this->contract->modification->last();
        if($lastModification){
            if($lastModification->editor_id){
                $editor = $lastModification->editor->nameAndRolByCompany($this->contract->company->id);
            }
        }

        return [
            "worker_id" => $this->worker->id,
            "contract_id" => $this->contract->id,
            "company_id" => $this->contract->company->id,
            "worker" => $this->worker->full_name,
            "company" => $this->contract->company->name_with_cif,
            "user" => $editor
        ];
    }
}
