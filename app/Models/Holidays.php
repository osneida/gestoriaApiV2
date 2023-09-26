<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;


class Holidays extends Model
{
    protected $guarded = ["id"];

    protected $appends = [
        "worker",
        "agreement_days",
        "difference",
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'id');
    }

    public function scopeFiltered(Builder $query)
    {
        $sortBy = request('sortBy') ? request('sortBy')[0] : "requested_start_date";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        $query->with(['contract' => function ($query) {
            $query->select('id', 'worker_id', 'company_id')->with(['worker']);
        }, 'approver']);

        $company = request('company');
        if ($company && $company != "undefined" && $company != "null") {
            $query->whereHas('contract', function ($query) use (&$company) {
                $query->where('company_id', $company);
            });
        }

        $worker = request('worker');
        if ($worker && $worker != "undefined" && $worker != "null") {
            $query->whereHas('contract', function ($query) use (&$worker) {
                $query->where('worker_id', $worker);
            });
        }
        $state = request('state');
        switch ($state) {
            case "aproved":
                $query->where("approved", TRUE);
                break;
            case "not_aproved":
                $query->where("approved", FALSE);
                break;
            case "pending":
                $query->whereNull("approved");
                break;
        }

        $start_list = request('start_list');
        if ($start_list && $start_list != "undefined" && $start_list != "null") {
            $query->whereDate("requested_start_date", ">=", $start_list);
        }


        $end_list = request('end_list');
        if ($end_list && $end_list != "undefined" && $end_list != "null") {
            $query->whereDate("requested_end_date", "<=", $end_list);
        }


        $start = request('start');
        $end = request('end');
        if ($start && $end) {

            $query->where(function ($q) use ($start, $end) {


                $q->where(function ($q) use ($start, $end) { //Si esta aprovada agafem dates finals
                    $q->where("approved", TRUE);
                    $q->whereDate("approved_start_date", "<=", $end);
                    $q->whereDate("approved_end_date", ">=", $start);
                });

                $q->orWhere(function ($q) use ($start, $end) { //Si no esta aprovada agafem dates request
                    $q->whereNull("approved");

                    $q->whereDate("requested_start_date", "<=", $end);
                    $q->whereDate("requested_end_date", ">=", $start);
                });
            });
        }

        $user = JWTAuth::parseToken()->authenticate();
        $type = request('type');
        Log::info('mensaje usuariooooooooooooo',['data'=>$user]);
        Log::info('mensaje tipooooooooooooooo',['data'=>$type]);
        switch ($type) {
            case "responsible":
                //Si es responsable filtrem nomes per els de la meva empresa
                
                if ($user->role->id === 2) {
                    $query->whereHas(
                        'contract',
                        function ($query) use ($user) {
                       
                            $query->whereHas('worker', function (Builder $q) {
                                $q->whereHas('companies', function ( $q) {
                                    $q->whereHas("users", function ($q) {
                                        $q->where("id", auth()->user()->id);
                                        $q->where('users_companies.role', 'gestor');
                                    });
                                });
                            });
                            $query->orWhere(function ($q) use ($user){
                                $q->whereHas('worker', function ($q) use ($user) {
                                    $q->whereHas("contracts", function ($q) use ($user){
                                        $q->where('creator_id', $user->id);
                                    });
                                    $q->whereHas('companies', function (Builder $q) use ($user){
                                        $q->whereHas("users", function ($q) use ($user){
                                            $q->where("id", $user->id);
                                            $q->where('users_companies.role', 'sub_gestor');
                                        });
                                    }); 
                                });
                            });
                            $query->orWhere(function ($q) use ($user){
                                $q->whereHas('worker', function ($q) use ($user) {
                                    $q->whereHas("contracts", function ($q) use ($user){
                                        $q->where('creator_id', $user->id);
                                    });
                                    $q->whereHas('companies', function (Builder $q) use ($user){
                                        $q->whereHas("users", function ($q) use ($user){
                                            $q->where("id", $user->id);
                                            $q->where('users_companies.role', 'worker');
                                        });
                                    }); 
                                });
                            });
                        }
                    );
                }
                
                if ($user->role->id === 3) {
                    $query->whereHas("contract", function ($q) use ($user) {
                        $q->whereHas("worker", function ($q) use ($user) {

                            $q->where("holiday_responsible", $user->id)


                                ->orWhere(function ($q) use ($user) {
                                    $q->whereNull("holiday_responsible")
                                        ->whereHas("companies", function ($q) use ($user) {
                                            $q->where("holiday_responsible", $user->id);
                                        });
                                });
                        });
                    });
                } else {
                    /*$query->whereHas("contract", function ($q) use ($user) {
                        $q->whereHas("worker", function ($q) use ($user) {
                            $q->where(function ($q) use ($user) {
                                $q->where("holiday_responsible", $user->id)


                                    ->orWhere(function ($q) use ($user) {
                                        $q->whereNull("holiday_responsible")
                                            ->whereHas("companies", function ($q) use ($user) {
                                                $q->where("holiday_responsible", $user->id);
                                            });
                                    });
                            })->orWhere(function ($q) use ($user) {
                                $q->whereNull("holiday_responsible")
                                    ->whereHas("companies", function ($q) {
                                        $q->whereNull("holiday_responsible");
                                    });
                            });
                        });
                    });*/
                }
                break;
            case "mine":
            default:
                $query->whereHas(
                    'contract',
                    function ($query) use ($user) {
                        $query->where('worker_id', $user->worker_id);
                    }
                );
                break;
        }



        switch ($sortBy) {
            case 'requested_start_date':
            case 'requested_end_date':
            case 'approved_start_date':
            case 'approved_end_date': {
                    $query->orderBy($sortBy, $order);
                }
        }
        return $query;
    }

    public function getWorkerAttribute()
    {
        return $this->contract->worker->full_name_with_dni_and_company;
    }

    public function getAgreementDaysAttribute()
    {
        $user = JWTAuth::parseToken()->authenticate();
    
        if($user->role->id == 1){
            return $this->contract->agreement->days_of_holidays;
        }
        return null;
    }

    public function getDifferenceAttribute()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if($user->role->id == 1){
            return intval($this->contract->agreement->days_of_holidays) - intval($this->total_spended_days);
        }
        return null;
        
    }
}
