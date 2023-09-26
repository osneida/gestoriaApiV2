<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{

    protected $fillable = [
        'event_date',
        'company',
        'name',
        'surname',
        'email',
        'identification_number',
        'phone_number',
        'workplace',
        'department',
        'reason_for_complaint',
        'description_of_events',
        'codigo'
    ];
    
    // Define relationship to multimedia table
    public function multimedia()
    {
        return $this->hasMany(ComplaintMultimedia::class);
    }
}

