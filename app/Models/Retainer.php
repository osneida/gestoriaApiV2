<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Retainer extends Model
{
    protected $table = 'retainers';
    protected $guarded = ['id'];
    public function contracts()
    {
        return $this->hasMany(Contract::class, "id", "contract_id");
    }

    public function scopeFiltered(Builder $query)
    {
        $query->select("id", "contract_id", 'import', "start_date","pay_received", 'created_at');

        if (request("id")) {
            $query->whereHas("contracts", function ($q) {
                $q->where('worker_id', request("id"))
                    ->whereDate("contract_start_date", "<=", now())
                    ->where(function ($q) {
                        $q->whereDate("contract_end_date", ">", now());
                        $q->orWhereNull("contract_end_date");
                    })
                    ->where([["salary", "<>", null], ["salary", "<>", 0]]);
            });
        }

        $sortBy = request('sortBy') ? request('sortBy')[0] : "start_date";
        $order = request('sortDesc') && request('sortDesc')[0] == 'false' ? 'asc' : 'desc';



        switch ($sortBy) {
            case 'start_date':
            case 'pay_received':
            case 'import': {
                    $query->orderBy($sortBy, $order);
                }
        }

        return $query;
    }
}
