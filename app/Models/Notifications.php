<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class Notifications extends Model
{
    //

    public $incrementing = false;
    protected $guarded = [];


    protected $casts = [
        'data' => 'array',
    ];

    protected $appends = ["notifiable"];

    public function notifiableUser()
    {
        return $this->belongsTo(User::class, "notifiable_id");
    }
    public function notifiableRole()
    {
        return $this->belongsTo(Role::class, "notifiable_id");
    }

    public function getNotifiableAttribute()
    {
        switch ($this->notifiable_type) {
            case Role::class:
                return $this->notifiableRole;
            case User::class:
                return $this->notifiableUser;
        }
    }

    public function scopeFiltered(Builder $query)
    {

        $role = request("role");

        if ($role) {
            $query->where("notifiable_type", Role::class);
            $query->where("notifiable_id", $role);
        }

        $user = request("user");

        if ($user) {
            $query->where("notifiable_type", User::class);
            $query->where("notifiable_id", $user);
        }

        //Log::debug($user);

        $from = request("from");
        if ($from) {
            $query->whereDate("created_at", ">=", $from);
        }

        $to = request("to");
        if ($to) {
            $query->whereDate("created_at", "<=", $to);
        }
        $query->orderBy('created_at', 'desc');


        return $query;
    }
}
