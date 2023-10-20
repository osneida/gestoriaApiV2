<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->wantsJson()) {
            $settings = Setting::all();
            return response()->json(["success" => true, 'settings' => $settings]);
        }
        abort(401);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        if (request()->wantsJson()) {
            foreach (request()->input() as $key => $val) {
                Setting::updateOrCreate(["key" => $key], ["val" => $val]);
            }
            return response()->json(["success" => true]);
        }
        abort(401);
    }
}
