<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{

    public function index()
    {
        $itemsPerPage = (int) request('itemsPerPage');
        $payrolls = Category::filtered();
        return response()->json($payrolls->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10));
    }
    public function store()
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();



                $category = Category::create([
                    "level" => strtoupper(request("level")),
                    "name" => request("name"),
                    "salary" => request("salary"),
                    "agreement_id" => request("agreement_id"),
                    "salary_by_hour" => request("salary_by_hour") ,
                    "has_salary_by_hour" => request("has_salary_by_hour")?? false,
                ]);



                DB::commit();
                return response()->json(["success" => true, "data" => $category]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }
    public function destroy($id)
    {


        $category = Category::find($id);
        $category->delete();
        return response()->json(["message" => "success"]);
    }

    public function show($id)
    {
        if (request()->wantsJson()) {
            $category = Category::where("agreement_id", $id)->get();
            return response()->json(["success" => true, "category" => $category]);
        }
    }
    public function showCategorySalary($id)
    {
        if (request()->wantsJson()) {
            $agreement = Category::select("id", "level", "salary", "name")->find($id);
            return response()->json(["success" => true, "agreement" => $agreement]);
        }
    }
    public function update(int $id)
    {
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $category = Category::find($id);
                if (!$category) {
                    return response()->json(["success" => false], 404);
                }






                if ($category) {
                    $category->level = request("level");
                    $category->name = request("name");
                    $category->salary = request("salary");
                    $category->salary_by_hour = request("salary_by_hour");
                    
                    $category->has_salary_by_hour = request("has_salary_by_hour") ?? false;




                    $category->save();
                    DB::commit();
                    return response()->json(["success" => true]);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }
}
