<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Contract;

use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationsController extends Controller
{
    //
    public function paginatedForReport()
    {

        $itemsPerPage = (int) request('itemsPerPage');
        $notifications = Notifications::filtered();
        return response()->json(["success" => true, "data" => $notifications->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }



    public static function notificateRole($notification, $role_id = Role::AGENCY)
    {
        $role = Role::find($role_id);
        $role->notify($notification);
    }

    public static function notificateUser($user_id, Notification $notification)
    {
        $role = User::find($user_id);
        $role->notify($notification);
    }

    public function getCounts()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return  [
            "role" => Notifications::where("notifiable_type", Role::class)->where("notifiable_id", $user->role->id)->whereNull("read_at")->count(),
            "user" => Notifications::where("notifiable_type", User::class)->where("notifiable_id", $user->id)->whereNull("read_at")->count()
        ];
    }

    public function readNotification($id)
    {
        Notifications::where("id", $id)->update([
            "read_at" => now()
        ]);
    }
}
