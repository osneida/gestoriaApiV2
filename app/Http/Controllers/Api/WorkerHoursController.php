<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkerHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $itemsPerPage = (int) request('itemsPerPage');
        $workers = WorkerHours::filtered();
        return response()->json(["success" => true, "data" => $workers->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }

    public function indexAll()
    {
        //
        $workers = WorkerHours::filtered();
        return response()->json(["success" => true, "data" => $workers->get()]);
    }


    public function sumHours()
    {
        //
        return WorkerHours::filtered()->sum("hours");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
        try {
            DB::beginTransaction();
            foreach (request("hours") as $h) {
                WorkerHours::updateOrCreate([
                    "contract_id" => $h["contract_id"],
                    "date" => $h["date"]
                ], [
                    "hours" => $h["hours"]
                ]);
            }
            DB::commit();

            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkerHours  $workerHours
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return  WorkerHours::where("id", $id)->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkerHours  $workerHours
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        WorkerHours::where("id", $id)->update(request()->only("date", "hours"));
        return response()->json(["success" => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkerHours  $workerHours
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        WorkerHours::where("id", $id)->delete();
    }
}
