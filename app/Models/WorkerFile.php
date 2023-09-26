<?php

namespace App\Models;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;

class WorkerFile extends Model
{
    //

    protected $guarded = ["id"];

    public function relatedFile()
    {
        return  $this->hasMany(WorkerFile::class, "related_file_id", "id");
    }
    public function worker(){
        return $this->hasOne(Worker::class, "id", "worker_id");
    }

    const TIME_SIGNATURE_S3_TEMPORARY_URL = 5;
}
