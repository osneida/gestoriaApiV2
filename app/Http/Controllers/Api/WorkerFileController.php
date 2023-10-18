<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Mail\BaixaMedicaDeletedNotification;
use App\Mail\BaixaMedicaModificationNotification;
use App\Mail\DeletedFile;
use App\Models\Worker;
use App\Models\WorkerFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class WorkerFileController extends Controller
{
    //

    public function index($worker_id)
    {
        return WorkerFile::where("worker_id", $worker_id)->get();
    }

    public function getFiltered($worker_id, $filter)
    {
        switch ($filter) {
            case "baixa": {
                    return self::getBaixes($worker_id);
                }
        }
    }

    public function getBaixes($worker_id)
    {
        $data["active"] = WorkerFile::where("worker_id", $worker_id)
            ->where("type", "baixa")
            ->with("relatedFile")
            ->whereDoesntHave("relatedFile", function ($query) {
                $query->where("type", "alta");
            })
            ->first();

        $data["previous"] = WorkerFile::where("worker_id", $worker_id)->where("type", "baixa")
            ->with("relatedFile")
            ->whereHas("relatedFile", function ($query) {
                $query->where("type", "alta");
            })
            ->get();;

        return response()->json($data);
    }

    public function store($worker_id)
    {
        try {
            $worker = Worker::where("id", $worker_id)->first();
            if (request()->hasFile('file')) {
                $file = request()->file('file');
                $filePath = sprintf("%s/%s", $worker->dni, $file->hashName());

                Storage::disk('s3')->put($worker->dni, $file);

                if (request('contract_id')) {
                    //TODO   S'hauria de mirar per borrar el contracte ja pujat en cas que existis
                    WorkerFile::updateOrCreate([
                        "contract_id" => request('contract_id'),
                        "type" => request("type"),
                    ], [
                        "worker_id" => $worker_id,
                        "file_route" => $filePath,
                    ]);
                } else if (request("baixa_id")) {
                    WorkerFile::create([
                        "worker_id" => $worker_id,
                        "file_route" => $filePath,
                        "type" => request("type"),
                        "related_file_id" => request("baixa_id")
                    ]);
                } else {
                    WorkerFile::create([
                        "worker_id" => $worker_id,
                        "file_route" => $filePath,
                        "type" => request("type"),
                    ]);
                }
            } else {
                return response()->json(["error" => "NO FILE"], 400);
            }

            return response()->json(["message" => "succes $worker_id"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }



    public function generateS3SignedUrl()
    {

        try {
            $url = Storage::disk('s3')->temporaryUrl(
                request('file_route'),
                Carbon::now()->addMinutes(WorkerFile::TIME_SIGNATURE_S3_TEMPORARY_URL)
            );
            return response()->json(["success" => true, "signed_url" => $url]);
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "error" => $exception->getMessage()], $exception->getCode());
        }
    }
    public function test()
    {
        return "test";
    }

    public function update($worker_id, $id)
    {

        $worker_file = WorkerFile::where("id", $id)->with(["worker"])->first();

        if (request()->hasFile('file')) {
            $file = request()->file('file');
            $filePath = sprintf("%s/%s", $worker_file->worker->dni, $file->hashName());

            //REMOVE OLD
            //Storage::disk('s3')->delete($worker_file->file_route);
            //UPDATE NEW
            while (!Storage::disk('s3')->exists($filePath))
                Storage::disk('s3')->put($worker_file->worker->dni, $file);


            //TODO Notificar de la modificacio

            if (Storage::disk('s3')->exists($filePath)) {
                $email = app()->environment('production') ? env("NOTIFICATION_MAIL") : env("DEVELOPER_MAIL");

                Mail::to($email)->send(new BaixaMedicaModificationNotification($worker_file));

                $worker_file->file_route = $filePath;
                $worker_file->save();
            }
        }
    }

    public function delete($worker_id, $id)
    {
        $worker_file = WorkerFile::where("id", $id)->with(["worker"])->first();

        if ($worker_file) {

            Storage::disk('s3')->delete($worker_file->file_route);

            //Notificar de la eliminacio
            $email = app()->environment('production') ? env("NOTIFICATION_MAIL") : env("DEVELOPER_MAIL");

            Mail::to($email)->send(new BaixaMedicaDeletedNotification($worker_file, $worker_file->worker));


            $worker_file->delete();
            return "success";
        }
        return "no file";
    }
}
