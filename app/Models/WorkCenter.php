<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCenter extends Model
{
    protected $fillable = ['name'];
    
    public function companies()
    {
        return $this->belongsTo(Company::class);
    }
}
