<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WorkerDni extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function scopeFiltered(Builder $query)
    {
        $company = request('company_id');

        $payrolls = $query->select(
            'id',
            'name',
            'dni',
            'period',
            'company_name',
            'company_id'
        );

        $sortBy = request('sortBy') ? request('sortBy')[0] : "company_name";
        $order  = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'name':
            case 'dni':
            case 'period':
            case 'company_name': {
                    $payrolls->orderBy($sortBy, $order);
                }
        }
 
        if ($company) {
            $payrolls->where("company_id", $company);
        }

        return $payrolls;
    }
}
