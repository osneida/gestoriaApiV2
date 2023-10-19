<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Irpf;
use Illuminate\Http\Request;

class IrpfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Irpf::filtered()->get();
    }

    public function update()
    {
        $current_ids = [];
        foreach (request("irpfs") as $irpf) {
            if (isset($irpf["id"])) {
                $id = $irpf["id"];
                unset($irpf["id"]);
                Irpf::where("id", $id)->update($irpf);
                $current_ids[] = $id;
            } else {
                $i = Irpf::create($irpf);
                $current_ids[] = $i->id;
            }
        }
        Irpf::whereNotIn("id", $current_ids)->delete();
    }
}
