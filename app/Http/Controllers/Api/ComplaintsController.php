<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplaintsEmail;
use App\Models\Complaint;
use App\Notifications\ComplaintsNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class ComplaintsController extends Controller
{
    public function store(Request $request)
    {   


        if(request("motivo")){
            $motivo =  request("motivo") !== "otros" ? request("motivo") : request("motivo_personalizado");
        }else{
            $motivo =  null;
        }

        $eventDate = request("fechaHechos") ?? null;

        if ($eventDate === "null" || $eventDate === null ) {
            $eventDate = null;
        }

        $clave = Str::random(10);
        Log::info('mensaje',['data'=>$clave]);
        $complaint = Complaint::create([
            "event_date" => $eventDate,
            "company" => request("company"),
            "name" => request("nombre"),
            "surname" => request("apellido"),
            "email" => request("email"),
            "identification_number" => request("nif"),
            "phone_number" => request("telefono"),
            "workplace" => request("centroTrabajo"),
            "department" => request("departamento"),
            "reason_for_complaint" => $motivo,
            "description_of_events" => request("descripcionHechos"),
            "codigo" => $clave,
        ]);

        $files = request()->file('archivos');

        if($files){
            $filePaths = [];
            foreach ($files as $file) {  
                $extension = $file->getClientOriginalExtension();
                $path = 'complaints/id-'.$complaint->id.'/' . uniqid() .'.'.$extension;
                $save = Storage::disk('s3_complaint')->put($path, File::get($file));
                $complaint->multimedia()->create([
                    "url" => $path,
                ]);

                $filePaths[] = $path;
            
            }
            $request->merge(['file_paths' => $filePaths]);
        }
        

        $usuarios = ComplaintsEmail::all();

        // Enviar la notificación a cada usuario
        foreach ($usuarios as $usuario) {
            $usuario->notify(new ComplaintsNotification($request));
        }
        
        return response()->json(['message' => 'Denuncia enviada correctamente']);
    }
}
