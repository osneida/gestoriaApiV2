<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMultimedia extends Model
{

    protected $fillable = [
        'url',
        'complaint_id'
    ];
    
    // Define relationship to denuncia table
    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
