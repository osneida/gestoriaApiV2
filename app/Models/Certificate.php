<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    //
    protected $guarded = ["id"];

    const TIME_SIGNATURE_S3_TEMPORARY_URL = 5;

    const MAX_ATTEMPTS = 2;
    protected $appends = [
        "processed_at_formatted"
    ];
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFiltered(Builder $query)
    {
        $company = request('company');
        $worker = request('worker_id');


        $query
            ->whereHas('worker')
            ->with(['company' => function ($q) {
                $q->select("id", "name", "cif");
            }])
            ->with(['worker' => function ($q) {
                $q->select("id", "first_name", "last_name", "dni");
            }]);


        $company = request('company');
        if ($company) {
            $query->whereHas('company', function (Builder $q) use ($company) {
                $q->where("id", $company);
            });
        }

        $worker = request('worker_id');
        if ($worker) {
            $query->whereHas('worker', function (Builder $q) use ($worker) {
                $q->where("id", $worker);
            });
        }

        $period = request('period');
        if ($period) {
            $query->where("period", $period);
        }

        $not_processed = request("not_processed");
        if ($not_processed == TRUE)
            $query->whereNull("processed");

        $sortBy = request('sortBy') ? request('sortBy')[0] : 'period';
        $order = request('sortDesc') && request('sortDesc')[0] == 'false' ? 'asc' : 'desc';

        switch ($sortBy) {
            case 'period': {
                    $query->orderBy($sortBy, $order);
                }
            default: {
                    $query->orderBy('period', 'desc');
                }
        }


        return $query;
    }

    public function getProcessedAtFormattedAttribute()
    {
        if ($this->processed) {
            /*return parse_date_to_gmt2_eloquent($this->processed);*/
            return $this->processed;
        }
    }
}
