<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    public $timestamps = false;
    
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
