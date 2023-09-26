<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    //
    protected $guarded = ['id'];
    
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function scopeFiltered(Builder $query)
    {
        $query->select('id', 'name', 'help_name','days_of_holidays', 'holidays_type');
        $search = request('search');

        if ($search && strlen($search) > 0) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $sortBy = request('sortBy') ? request('sortBy')[0] : "first_name";
        $order = request('sortDesc') && request('sortDesc')[0] == 'true' ? 'desc' : 'asc';
        
        
        
        switch ($sortBy) {
            case 'name':
            case 'help_name': {
                    $query->orderBy($sortBy, $order);
                }
        }
        return $query;
    }
}
