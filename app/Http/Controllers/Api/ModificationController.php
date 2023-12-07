<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Modification;
use Illuminate\Http\Request;

class ModificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paginatedForReport()
    {

        $itemsPerPage = (int) request('itemsPerPage');
        $modification = Modification::filtered();
        return response()->json(["success" => true, "data" => $modification->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }

    public function filterInfo()
    {

        $filter["type"] = Modification::select("type")
            ->groupBy("type")
            ->get();

        return $filter;
    }
}
