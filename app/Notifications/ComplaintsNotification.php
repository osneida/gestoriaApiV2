<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComplaintsNotification extends Notification
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            $mensaje = new MailMessage;
            #Log::info('mensaje dataaaaaaaaaaaaaaaa mail',['data'=>$this->data]);
            if(!$this->data['anonimo']){
                $this->data['anonimo'] = 'No';
            }
            #Log::info('mensaje pasa por aca 1');
            if($this->data['motivo']){
                #Log::info('mensaje pasa por aca 2');
                $motivo =  $this->data['motivo'] !== "otros" ? $this->data['motivo'] : $this->data['motivo_personalizado'];
            }else{
                $motivo =  "";
            }

            $descripcion = $this->data['descripcionHechos'];

            if ($descripcion) {
                $codigo = DB::table('complaints')
                    ->where('description_of_events', $descripcion)
                    ->value('codigo');
                #Log::info('mensaje dataaaaaaaaaaaaaaaa mail',['data'=>$codigo]);
            } else {
                $codigo = null;
            }


            $mensaje->subject('Nueva denuncia recibida')
                    ->greeting('Hola,')
                    ->line('Se ha recibido una nueva denuncia')
                    ->line('data del solicitante:')
                    ->line('Código de la denuncia: ' . $codigo)
                    ->line('Denuncia anónima: '.$this->data['anonimo'])
                    ->line('Fecha de los hechos: '.$this->data['fechaHechos'])
                    ->line('Empresa: '.$this->data['company'])
                    ->line('Persona Afectada o Testigo: '.$this->data['personaAfectadaTestimoni'])
                    ->line('Nombre: '.$this->data['nombre'])
                    ->line('Apellido: '.$this->data['apellido'])
                    ->line('Correo electrónico: '.$this->data['email'])
                    ->line('NIF: '.$this->data['nif'])
                    ->line('Teléfono de contacto: '.$this->data['telefono'])
                    ->line('Centro de trabajo: '.$this->data['centroTrabajo'])
                    ->line('Departamento: '.$this->data['departamento'])
                    ->line('Motivo de la denuncia: '.$motivo)
                    ->line('Descripción de los hechos: '.$this->data['descripcionHechos'])
                    ->line('Archivos adjuntos:');

                    // Adjuntar los archivos
                    if($this->data['file_paths']){
                        foreach ($this->data['file_paths'] as $nameFile) {
                            $url = Storage::disk('s3_complaint')->url($nameFile); // Generar la URL del archivo en AWS
                            $mensaje->line($url); // Agregar el enlace al mensaje
                        }
                    }
                   
            return $mensaje;
        } catch (\Exception $e) {
            // Manejar la excepción aquí
            // por ejemplo, puedes mostrar un mensaje de error o registrar la excepción en un archivo de registro.
            echo 'Error: ' . $e->getMessage();
        }
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
