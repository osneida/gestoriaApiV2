<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Payroll
 *
 * @property int $id
 * @property int $worker_id
 * @property int $company_id
 * @property string|null $checksum
 * @property string $document_file
 * @property string|null $processed
 * @property string $period Periodo a procesar la nómina
 * @property int $attempts Número de veces que se ha intentado enviar, report de error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read mixed $processed_at_formatted
 * @property-read \App\Models\Worker $worker
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll filtered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereDocumentFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Payroll whereWorkerId($value)
 * @mixin \Eloquent
 */
class Payroll extends Model
{
    protected $guarded = ["id"];

    protected $appends = [
        "processed_at_formatted"
    ];

    const TIME_SIGNATURE_S3_TEMPORARY_URL = 5;

    const MAX_ATTEMPTS = 2;

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFiltered(Builder $query)
    {
        $company = request('company');
        $worker = request('worker');


        $payrolls = $query->select(
            'id',
            'document_file',
            'period',
            'worker_id',
            'company_id',
            'processed',
            "opened"
        )
            ->whereHas('worker')
            ->with(['company' => function (BelongsTo $q) {
                $q->select("id", "name", "cif");
            }])
            ->with(['worker' => function (BelongsTo $q) {
                $q->select("id", "first_name", "last_name", "dni");
            }]);
        //->where("processed", 1);

        if ($company) {
            $payrolls->whereHas('company', function (Builder $q) use ($company) {
                $q->where("id", $company);
            });
        }
        $worker = request("worker_id");
        if ($worker) {
            $payrolls->whereHas('worker', function (Builder $q) use ($worker) {
                $q->where("id", $worker);
            });
        }
        $payrolls->whereHas('worker', function (Builder $q) use ($worker) {
            $q;
        });

        if (auth()->user()->role_id === User::COMPANY_ROLE) {

    // $payrolls->whereHas('company', function (Builder $q) {
                        //     $q->where("id", auth()->user()->company_id);
                        // });
            $payrolls->where(function ($payrolls) {
                $payrolls->whereHas('worker', function (Builder $q) {
                    $q->whereHas('companies', function ( $q) {
                        $q->whereHas("users", function ($q) {
                            $q->where("id", auth()->user()->id);
                            $q->where('users_companies.role', 'gestor');
                        });
                    });
                });
                $payrolls->orWhere(function ($q) {
                    $q->whereHas('worker', function ($q) {
                        $q->whereHas("contracts", function ($q) {
                            $q->where('creator_id', auth()->user()->id);
                        });
                        $q->whereHas('companies', function (Builder $q) {
                            $q->whereHas("users", function ($q) {
                                $q->where("id", auth()->user()->id);
                                $q->where('users_companies.role', 'sub_gestor');
                            });
                        }); 
                    });
                    
                });
            });

        

        }

        $start = request("start");
        if ($start) {
            $payrolls->where("period", ">=", $start);
        }

        $end = request("end");
        if ($end) {
            $payrolls->where("period", "<=", $end);
        }

        $sortBy = request('sortBy') ? request('sortBy')[0] : 'period';
        $order = request('sortDesc') && request('sortDesc')[0] == 'false' ? 'asc' : 'desc';

        switch ($sortBy) {
            case 'period': {
                    $payrolls->orderBy($sortBy, $order);
                }
            default: {
                    $payrolls->orderBy('period', 'desc');
                }
        }

        return $payrolls;
    }

    public function getProcessedAtFormattedAttribute()
    {
        if ($this->processed) {
            /*return parse_date_to_gmt2_eloquent($this->processed);*/
            return $this->processed;
        }
    }
}
