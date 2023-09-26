<?php

namespace App\Models;

use App\Models\WorkerFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Worker
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string $dni
 * @property string $document_type
 * @property string|null $id_document_file
 * @property string|null $ss_number
 * @property string|null $ss_document_name
 * @property string|null $ss_document_file
 * @property string|null $contract_type
 * @property string|null $contract_period
 * @property string|null $contract_reason
 * @property string|null $contract_weekly_hours
 * @property string|null $contract_working_hours
 * @property string|null $contract_schedule
 * @property string|null $hiring_date
 * @property string|null $agreement convenio
 * @property int|null $gross_salary
 * @property int|null $net_salary
 * @property int|null $number_of_payments número de pagas
 * @property string|null $iban
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read int|null $companies_count
 * @property-read mixed $full_name_with_dni
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker filtered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractWeeklyHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereContractWorkingHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereGrossSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereHiringDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereIdDocumentFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereNetSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereNumberOfPayments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereSsDocumentFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereSsDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereSsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Worker whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Worker extends Model
{
    use SoftDeletes;

    public const WEEKLY_HOURS = ['Completa', 'Parcial'];
    public const NUMBER_OF_PAYMENTS = [12, 13, 14, 15];
    public const DNI = 'dni';

    protected $guarded = ["id"];

    protected $appends = [
        "full_name_with_dni",
        "full_name_with_dni_and_company",
        "full_name",
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function latestContract()
    {
        return $this->contracts()->orderBy('contract_start_date', "DESC")->orderBy('contract_end_date', "DESC");
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->hasOne(User::class, "worker_id", "id");
    }
    public function files()
    {
        return $this->hasMany(WorkerFile::class);
    }

    public function responsible()
    {
        return $this->hasOne(User::class, "id", "holiday_responsible");
    }

    public function workCenter()
    {
        return $this->belongsTo(WorkCenter::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFiltered(Builder $query)
    {
        $search = request('search');
        $company = request('company');

        $inactive = request('inactive');

        $workers = $query->select(
            'id',
            'first_name',
            'last_name',
            'email',
            'dni',
            'work_center_id'
        )
            ->whereHas('companies', function ($q) use ($inactive) {
                /*
                $q->where('active', $inactive === "true");
                */
            })
            ->with(['companies' => function (BelongsToMany $q) use ($inactive, $company) {
                /*$q->select("id", "name")->where('active', $inactive === "true")->latest();*/
                if ($company) {
                    $q->where("id", $company);
                }
            }])

            ->whereNotNull("first_name");


        /*
        if ($inactive === "false") {
            $workers->where("archive", true);
        } else {
            $workers->where("archive", false);
        }
        */

        if ($company) {
            $workers->whereHas('companies', function (Builder $q) use ($company) {
                $q->where("id", $company);
            });
        }

        if (auth()->user()->role_id === User::COMPANY_ROLE) {
            $workers->where(function ($workers) {
                $workers->whereHas('companies', function (Builder $q) {
                    $q->whereHas("users", function ($q) {
                        $q->where("id", auth()->user()->id);
                        $q->where('users_companies.role', 'gestor');
                    });
                });
                $workers->orWhere(function ($q) {
                    $q->whereHas('contracts', function ($q) {
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


            $type = request("type");

            $month = explode("-", request("month"));
            $typeDate = request("typeDate");

            switch ($type) {

                case "inscrition":
                    $query->whereHas('contracts', function ($query) use ($month, $typeDate) {
                        $query->whereYear('contract_start_date', '=', $month[0]);
                        if ($typeDate == 'mensual' || $typeDate == null) $query->whereMonth('contract_start_date', '=', $month[1]);
                        $query->whereNull('modificated_contract_id');
                    });
                    break;
                case "modification":
                    $query->whereHas('contracts', function ($query) use ($month, $typeDate) {
                        $query->whereYear('created_at', '=', $month[0]);
                        if ($typeDate == 'mensual' || $typeDate == null)   $query->whereMonth('created_at', '=', $month[1]);
                        $query->whereNotNull('modificated_contract_id');
                    });
                    break;
                case "terminated":
                    $query->whereHas('contracts', function ($query) use ($month, $typeDate) {
                        $query->whereYear('updated_at', '=', $month[0]);
                        if ($typeDate == 'mensual' || $typeDate == null)   $query->whereMonth('updated_at', '=', $month[1]);
                        $query->whereNotNull('not_enjoyed_vacancies');
                    });
                    break;
            }
        }

        if ($search && strlen($search) > 0) {
            $workers->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', "%$search%");
        }

        /*
        $contract = request("contract");
        if ($contract) {
            $workers->whereHas('contracts', function (Builder $q) use ($contract) {
                $q->where("contract_type", $contract);
                $q->whereDate("contract_start_date", "<=", now());
              $q->where(function ($q) {
                    $q->whereDate("contract_end_date", ">=", now());
                    $q->orWhere("contract_end_date", null);
                });
            });
        }
        */

        $contract = request("contract");

        switch (request("type")) {
            case "archived":

                $workers->where("archive", true);
                $workers->with(['latestContract' => function ($q) use ($company, $contract) {
                    $q->select("id", "contract_type", "contract_start_date", "contract_end_date", "worker_id", "end_motive", "notificated_at");
                    $q->where(function ($q) {
                        $q->whereDate("contract_end_date", "<=", now());
                        $q->orWhereNull("contract_end_date");
                    });
                    if ($company) {

                        $q->where("company_id", $company);
                    }
                }]);

                break;
            case "future":

                $workers->with(['latestContract' => function ($q) use ($company, $contract) {
                    $q->select("id", "contract_type", "contract_start_date", "contract_end_date", "worker_id", "end_motive", "notificated_at");

                    $q->whereDate("contract_start_date", ">=", now());

                    if ($company) {
                        $q->where("company_id", $company);
                    }
                }]);
                $workers->where(function($workers) use ($contract){

                    $workers->whereHas("latestContract", function ($q) use ($contract) {
                        $q->whereDate("contract_start_date", ">=", now());
                        
                        if ($contract)
                        $q->where("contract_type", $contract);
                    });
                    $workers->orWhere(function($q){
                        $q->whereDoesntHave('latestContract');
                    });
                });
                break;
            case "active":
            default:
                $workers->with(['latestContract' => function ($q) use ($company, $contract) {
                    $q->select("id", "contract_type", "contract_start_date", "contract_end_date", "worker_id", "end_motive", "notificated_at");

                    $q->whereDate("contract_start_date", "<=", now());
                    $q->where(function ($q) {
                        $q->whereDate("contract_end_date", ">=", now());
                        $q->orWhereNull("contract_end_date");
                    });

                    if ($company) {
                        $q->where("company_id", $company);
                    }
                }])->whereHas("latestContract", function ($q) use ($contract) {
                    $q->whereDate("contract_start_date", "<=", now());
                    $q->where(function ($q) {
                        $q->whereDate("contract_end_date", ">=", now());
                        $q->orWhereNull("contract_end_date");
                    });
                    if ($contract)
                        $q->where("contract_type", $contract);
                });
        }

        $email = request("email");
        if ($email === "true") {
            $workers->whereNull("email");
        }

        $sortBy = request('sortBy') ? request('sortBy')[0] : "first_name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'first_name':
            case 'last_name':
            case 'dni':
            case 'email': {
                    $workers->orderBy($sortBy, $order);
                }
        }

        return $workers;
    }

    public function getFullNameWithDniAttribute()
    {
        return sprintf('%s %s (%s)', $this->first_name, $this->last_name, $this->dni);
    }

    public function getFullNameWithDniAndCompanyAttribute()
    {
        return sprintf('%s %s (%s)(%s)', $this->first_name, $this->last_name, $this->dni,$this->companies->pluck('name')->implode(', '));
        //return $this->full_name . "(" . $this->dni . ")" .  "(" .$this->companies->pluck('name')->implode(', '). ")" .;
    }

    public function getFullNameAttribute()
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }
}
