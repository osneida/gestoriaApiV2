<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Retainer;

class RetainerController extends Controller
{
    public function index()
    {
        $retainers = Retainer::filtered();
        $itemsPerPage = (int) request('itemsPerPage');
        return response()->json(["import" => $retainers, "data" => $retainers->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 3)]);
    }
    public function store()
    {
        try {
            DB::beginTransaction();
            $retainers = Retainer::create([
                "contract_id" => request("contract_id"),
                "import" => request("import"),
                "start_date" => request("start"),
                "pay_received" => false
            ]);

            DB::commit();
            return response()->json(["success" => true, "import" => $retainers]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function update()
    {
        try {
            DB::beginTransaction();
            if (request("pay") !== null) {
                $retainers = Retainer::where("id", request("id"))->update([


                    "pay_received" => request("pay") == false ? false : true
                ]);
            } else {

                $retainers = Retainer::where("id", request("id"))->update([

                    "import" => request("import"),
                    "start_date" => request("start"),

                ]);
            }

            DB::commit();
            return response()->json(["success" => true, "import" => $retainers]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
    public function show($id)
    {
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            Retainer::where("id", $id)->delete();

            DB::commit();
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "error" => $exception->getMessage()], 400);
        }
    }
}
