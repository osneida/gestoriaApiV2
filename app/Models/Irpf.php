<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Irpf extends Model
{
    //
    protected $guarded = ["id"];


    public function scopeFiltered(Builder $query)
    {
        return $query;
    }
}
