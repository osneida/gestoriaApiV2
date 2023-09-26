<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class Documents extends Model
{
    protected $guarded = ['id'];

    const TIME_SIGNATURE_S3_TEMPORARY_URL = 5;

    public function users()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeFiltered(Builder $query)
    {
        $query->with(["company", "users"]);
    
        $u = JWTAuth::parseToken()->authenticate();
    
        switch ($u->role->id) {
            case Role::WORKER:
                $query->where("workers", true);
                break;
            case Role::COMPANY:
                $query->where(function ($q) {
                    $q->whereHas("company.users", function ($q) {
                        $q->where("users.id", auth()->user()->id);
                        $q->where('users_companies.role', 'gestor');
                    });
                    $q->orWhereHas("company.users", function ($q) {
                        $q->where("users.id", auth()->user()->id);
                        $q->where('users_companies.role', 'sub_gestor');
                    });
                });
                break;
        }

        return $query;
    }

}
