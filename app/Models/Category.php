<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];
    //
    public function Agreement()
    {
    }
    public function scopeFiltered(Builder $query)
    {
        $query->select('id', 'level', 'salary', 'name', 'salary_by_hour');
        $sortBy = request('sortBy') ? request('sortBy')[0] : "first_name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        $query->where("agreement_id", request("id"));


        switch ($sortBy) {
            case 'level':
                $query->orderBy($sortBy, $order);
            default:
                $query->orderBy('level', 'asc');
        }
        return $query;
    }
}
