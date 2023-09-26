<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorkerHours extends Model
{
    //
    protected $guarded = ["id"];



    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }


    public function scopeFiltered(Builder $query)
    {

        $query->with("contract");

        $worker_id = request("worker_id");

        if ($worker_id) {
            $query->whereHas("contract", function ($q) use ($worker_id) {
                $q->where("worker_id", $worker_id);
            });
        }


        $company_id = request("company_id");

        if ($company_id) {
            $query->whereHas("contract", function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
            });
        }

        $start = request("start");
        if ($start)
            $query->whereDate("date", ">=", $start);


        $end = request("end");

        if ($end)
            $query->whereDate("date", "<=", $end);

        return $query;
    }
}
