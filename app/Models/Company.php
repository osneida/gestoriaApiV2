<?php

namespace App\Models;

use App\Segment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Company
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string $cif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $name_with_cif
 * @property-read mixed $slug_name
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Worker[] $workers
 * @property-read int|null $workers_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company filtered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUserId($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    protected $guarded = ["id"];

    protected $appends = [
        "slug_name",
        "name_with_cif"
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, "company_id", "id");
    }

    public function getSlugNameAttribute()
    {
        return Str::slug($this->name, "-");
    }

    public function payrolPeriod()
    {
        return $this->hasMany(PayrollPeriod::class, "company_id", "id");
    }
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, "company_id", "id");
    }
    public function responsible()
    {
        return $this->hasOne(User::class, "id", "holiday_responsible");
    }
    public function agreements()
    {
        return $this->belongsToMany(Agreement::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_companies');
    }
    public function workCenters()
    {
        return $this->hasMany(WorkCenter::class);
    }
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFiltered(Builder $query)
    {
        $search = request('search');

        $companies = $query->select(
            'id',
            'name',
            'cif',
            'workers_access',
            'holiday_responsible',
            'sodexo',
            "has_worker_hors",
            "has_shift_control",
            "holidays",
            "complaints_channels",
        )
            ->withCount(['workers' => function ($q) {
                $q->whereHas("contracts", function ($q) {
                    $q->whereDate("contract_start_date", "<=", now());
                    $q->where(function ($q) {

                        $q->whereDate("contract_end_date", ">=", now());
                        $q->orWhereNull("contract_end_date");
                    });
                })->where("archive", false);
            }])
            ->whereNotNull("name");


        $inactive = request('inactive');
        if ($inactive) {
            $companies->where('active', $inactive === "true");
        }
        if ($search && strlen($search) > 0) {
            $companies->where('name', 'LIKE', "%$search%");
        }


        $sortBy = request('sortBy') ? request('sortBy')[0] : "name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';

        switch ($sortBy) {
            case 'name':
            case 'cif': {
                    $companies->orderBy($sortBy, $order);
                }
        }
        return $companies;
    }

    public function getNameWithCifAttribute()
    {
        return sprintf('%s (%s)', $this->name, $this->cif);
    }
}
