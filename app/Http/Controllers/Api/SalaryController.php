<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
    public function index()
    {
        $salarys = Salary::filtered();
        $itemsPerPage = (int) request('itemsPerPage');
        return response()->json(["data" => $salarys->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 3)]);
    }
    public function store()
    {
        try {
            DB::beginTransaction();
            $salarys = Salary::create([
                "contract_id" => request("contract_id"),
                "salary" => request("salary"),
                "start_date" => date("Y-m-h", strtotime(request("start"))),
                "end_date" => request("end"),
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $salarys]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function update()
    {
        try {
            DB::beginTransaction();
            $salarys = Salary::where("id", request("id"))->update([

                "salary" => request("salary"),
                "start_date" => request("start"),
                "end_date" => request("end") ? request("end") : null,
            ]);

            DB::commit();
            return response()->json(["success" => true, "salary" => $salarys]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function getAll($id)
    {
        return Salary::where("contract_id", $id)->orderBy('start_date')->first();
    }
    public function destroy()
    {
        try {
            DB::beginTransaction();
            Salary::where("id", request("id"))->delete();

            DB::commit();
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
}