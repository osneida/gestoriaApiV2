<?php

namespace App\Notifications\Contract;

use App\Mail\BaixaContracte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Baixa extends Notification
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
    public function __construct($contract, $worker)
    {
        $this->contract = $contract;
        $this->worker = $worker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->contract->end_motive != 2)
            return ['mail', "database"];
        return ["database"];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new BaixaContracte(
            $this->contract,
            $this->worker,
            $this->contract->company,
            $this->contract->end_motive
        ))->to($notifiable->email);;
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
