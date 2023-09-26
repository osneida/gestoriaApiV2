<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Salary extends Model
{
    protected $guarded = ['id'];
    protected $table = 'salary';
    public function contracts()
    {
        return $this->hasOne(Contract::class, "id", "contract_id");
    }

    public function scopeFiltered(Builder $query)
    {
        $query->select("id", "contract_id", 'salary', "start_date", "end_date", 'created_at');


        if (request("id")) {
            $query->whereHas("contracts", function ($q) {
                $q->where('worker_id', request("id"));
                //->whereDate("contract_start_date", "<=", now())
                //->where([["salary", "<>", null], ["salary", "<>", 0]]);
            });
        }
        $sortBy = request('sortBy') ? request('sortBy')[0] : "start_date";
        $order = request('sortDesc') && request('sortDesc')[0] == 'false' ? 'asc' : 'desc';



        switch ($sortBy) {
            case 'start_date':
            case 'salary': {
                    $query->orderBy($sortBy, $order);
                }
        }

        return $query;
    }
}
