<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::filtered();
        $itemsPerPage = (int) request('itemsPerPage');
        return response()->json(["import" => $commissions, "data" => $commissions->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 3)]);
    }
    public function store()
    {
        try {
            DB::beginTransaction();
            $commissions = Commission::create([
                "contract_id" => request("contract_id"),
                "import" => request("import"),
                "start_date" => request("start"),
                "observation" => request("observation"),
                "type" => request("type")
            ]);

            DB::commit();
            return response()->json(["success" => true, "import" => $commissions]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function update()
    {
        try {
            DB::beginTransaction();
            $commissions = Commission::where("id", request("id"))->update([
                "observation" => request("observation"),
                "import" => request("import"),
                "start_date" => request("start"),
                "type" => request("type")
            ]);

            DB::commit();
            return response()->json(["success" => true, "import" => $commissions]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function show($id)
    {
       
    }
    public function destroy($id){
        try {
            DB::beginTransaction();
            Commission::where("id", $id)->delete();

            DB::commit();
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
}