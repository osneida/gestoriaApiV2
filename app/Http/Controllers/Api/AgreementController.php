<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;


class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (JWTAuth::parseToken()->authenticate()->role->id == 1)
            $agreements = Agreement::select('id', 'name', 'help_name')->with(['categories', 'companies'])->whereNotNull('name')->orderBy('name')->get();
        else
            $agreements = Agreement
                ::select('id', 'name', 'help_name')
                ->with(['categories', 'companies'])
                ->whereNotNull('name')
                ->whereHas('companies', function ($query) {
                    $query->whereHas("users", function ($q) {
                        $q->where("id", auth()->user()->id);
                        $q->whereIn('users_companies.role', ['gestor', 'sub_gestor']);
                    });
                })->orderBy('name')
                ->get();


        if (request()->wantsJson()) {
            return response()->json(["agreements" => $agreements]);
        }
    }


    public function getAll()
    {
        return Agreement::select('id', 'name', 'help_name')->get();
    }


    public function getList()
    {
        if (request()->wantsJson()) {
            $agreements = Agreement::filtered();
            $itemsPerPage = (int) request('itemsPerPage');
            return response()->json(["success" => true, "data" => $agreements->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $rules = [
                    "name" => 'required|unique:agreements|min:2|max:150',
                    "help_name" => 'required|unique:agreements|max:200'
                ];
                $validator = Validator::make(request()->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ));
                }


                $agreement = Agreement::create([
                    "name" => strtoupper(request("name")),
                    "help_name" => request("help_name"),
                    "days_of_holidays" => request("days_of_holidays"),
                    "holidays_type" => request("holidays_type")
                ]);

                Category::create([
                    "level" => "-",
                    "name" => "Administrador",
                    "salary" => "0.00",
                    "agreement_id" => $agreement->id,
                ]);
                Category::create([
                    "level" => "-",
                    "name" => "Becario",
                    "salary" => "0.00",
                    "agreement_id" => $agreement->id,
                ]);

                DB::commit();
                return response()->json(["success" => true, "data" => $agreement]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (request()->wantsJson()) {
            $agreement = Agreement::select("id", "name", "help_name", "days_of_holidays", "holidays_type")->find($id);
            return response()->json(["success" => true, "agreement" => $agreement]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $agreement = Agreement::find($id);
                if (!$agreement) {
                    return response()->json(["success" => false], 404);
                }
                $rules = [
                    "name" => 'required|unique:agreements,name,' . $id . '|min:2|max:150',

                ];
                $validator = Validator::make(request()->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 422);
                }


                if ($agreement) {
                    $agreement->name = request("name");
                    $agreement->help_name = request("help_name");
                    $agreement->days_of_holidays = request("days_of_holidays");
                    $agreement->holidays_type = request("holidays_type");

                    $agreement->save();
                    DB::commit();
                    return response()->json(["success" => true]);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }
    public function activeToogle($id)
    {
        $agreement = Agreement::where("id", $id);

        return $agreement->update([
            "active" => !$agreement->first()->active
        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            Agreement::where("id", $id)->delete();
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {

            return response()->json(["success" => false, "data" => $exception->getMessage()], 400);
        }
    }
}
