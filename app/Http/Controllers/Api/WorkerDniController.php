<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkerDni;
use Illuminate\Http\Request;

class WorkerDniController extends Controller
{
    public function index()
    {
        $itemsPerPage = (int) request('itemsPerPage'); 
        $workers = WorkerDni::paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 50);
        return response()->json(["success" => true, "data" => $workers]); 
    }

    public function store(Request $request)
    {
        foreach (request("worker") as $h) {
            WorkerDni::updateOrCreate([
                "dni"        => $h["dni"],
                "period"     => $h["period"],
                "company_id" => $h["company_id"],
            ], [
                "first_name"    => $h["first_name"],
                "last_name"     => $h["last_name"],
                "company_name"  => $h["company_name"]
            ]);
        }

        return response()->json(["success" => true]);
    }


    public function show()
    {
        $company_id = (int) request('company_id');
        $workers = WorkerDni::where('company_id', $company_id)->orderBy('id')->get();
        return response()->json(["success" => true, "data" => $workers]); 
    }

    public function destroy($id)
    {
        WorkerDni::where("id", $id)->delete();
        return response()->json(["success" => true]);
    }

    public function allFiltered()
    {
        $itemsPerPage = (int) request('itemsPerPage'); 
        $workers = WorkerDni::filtered();
        return response()->json(["success" => true, "data" => $workers->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 50)]);
    }
}

