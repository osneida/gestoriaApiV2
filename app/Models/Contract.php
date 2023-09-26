<?php

namespace App\Models;

use App\Models\WorkerFile;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    const MAX_ATTEMPTS = 3;
    const DAYS_FOR_SEND_NOTIFICATIONS = 10;

    protected $guarded = ["id"];

    protected $casts = [
        "iban" => "string"
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function salary()
    {
        return $this->hasMany(Salary::class, "contract_id", "id");
    }

    public function agreement()
    {
        return $this->hasOne(Agreement::class, "id", "agreement_id");
    }

    public function category()
    {
        return $this->hasOne(Category::class, "id", "category_id");
    }
    public function file()
    {
        return $this->hasMany(WorkerFile::class, "contract_id", "id");
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, "contract_type", "id");
    }

    public function holidays()
    {
        return $this->hasMany(Holidays::class, "contract_id", "id");
    }
    public function workerFiles()
    {
        return $this->hasMany(WorkerFile::class);
    }

    public function modification()
    {
        return $this->hasMany(Modification::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,"creator_id", "id");
    }
}
