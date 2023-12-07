<?php

namespace App\Models;

use App\Mail\ResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $role_id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    use CanResetPassword;

    const AGENCY_ROLE = 1;
    const COMPANY_ROLE = 2;
    const WORKER_ROLE = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'company_id', 'passwordChanged', "worker_id"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'users_companies')->withPivot("role");
    }

    public function worker()
    {
        return $this->hasOne(Worker::class, 'id', 'worker_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'creator_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            "id" => $this->id,
            "worker_id" => $this->worker_id,
            "email" => $this->email,
            "name" => $this->name,
            "role" => $this->role,
            "company" => $this->company,
            "companies" => $this->companies,
            "passwordChanged" => $this->passwordChanged
        ];
    }


    public function scopeFiltered(Builder $query)
    {


        $sortBy = request('sortBy') ? request('sortBy')[0] : "name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';
        $users = $query->select("id", "role_id", "name", "email", "company_id")->with(['company' => function (HasOne $q) {
            $q->select("id", "name")->latest();
        }]);
        $search = request('search');

        if ($search && strlen($search) > 0) {
            $users->where(function ($user) use ($search) {

                $user->where('name', 'LIKE', "%$search%");
                $user->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $company = request("company");
        if ($company) {
            $users->whereHas('company', function ($query) use ($company) {
                $query->where('id', $company);
            });
        }

        $role = request("role");
        if ($role) {
            $users->where('role_id', 'LIKE', "%$role%");
        }

        switch ($sortBy) {
            case 'name':
            case 'email': {
                    $users->orderBy($sortBy, $order);
                }
        }


        if (request("archived") === "true") {
            $users->where('archive', true);
        } else {
            $users->where('archive', false);
        }

        return $users;
    }

    public function sendPasswordResetNotification($token)
    {
        //Log::info($token);
        $email = app()->environment('production') ? $this->email : env("DEVELOPER_MAIL");

        Mail::to($this->email)->send(new ResetPassword($token, $this->email));
    }


    public function nameAndRolByCompany($company_id)
    {
        $company = $this->companies->find($company_id);

        if($company){
            $role = $company->pivot->role;

            if($role == 'sub_gestor'){
                $nameRol = "Subgestor";
            }

            if($role == 'gestor'){
                $nameRol = "Gestor";
            }
            
            return $this->name . " (".$nameRol.")";
        }else{
            return $this->name .' (sense càrrec)';
        }
        
    }
}
