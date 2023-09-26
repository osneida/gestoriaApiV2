<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{

    protected $guarded = ["id"];
    protected $table = "historial_modifications";
    public function contracts()
    {
        return $this->hasMany(Contract::class, "id", "contract_id");
    }
    public function editor()
    {
        return $this->hasOne(User::class, "id", "editor_id");
    }
    public function scopeFiltered(Builder $query)
    {

        $sortBy = request('sortBy') ? request('sortBy')[0] : "id";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'asc' : 'desc';


        $query->select("contract_id", "type", "start_date", "motive", "created_at")->with(["contracts" => function ($q) {
            $q->with(["worker", "company"]);
        }]);
        $company = request("company");
        if ($company) {
            $query->whereHas("contracts", function ($q) use($company){
                $q->where("company_id", $company);
            });
        }
        $type = request("type");
        if ($type) {
            $query->where("type", $type);
        }


        switch ($sortBy) {
            case 'id': {
                    $query->orderBy($sortBy, $order);
                }
        }
        return $query;
    }
}
