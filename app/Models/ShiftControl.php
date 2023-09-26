<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShiftControl extends Model
{
    //
    //
    protected $guarded = ["id"];



    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }


    public function scopeFiltered(Builder $query)
    {

        $from = request("from");
        if ($from)
            $query->whereDate("date", ">=", $from);

        $to = request("to");
        if ($to) $query->whereDate("date", "<=", $to);

        $worker_id = request("worker_id");
        if ($worker_id)
            $query->whereHas("contract", function ($q) use ($worker_id) {
                $q->where("worker_id", $worker_id);
            });
        $query->orderBy("hour", "asc");
        return $query;
    }
}
