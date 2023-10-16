<?php

use App\Http\Controllers\Api\PayrollController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('authenticate', [AuthController::class, 'login']);
Route::post('forgot_password', [AuthController::class, 'forgotPassword']); 
Route::post('reset_password', [AuthController::class, 'resetPassword']); 

//Route::post('reset_password', 'Api\\AuthController@resetPassword');

Route::get('phpinfo', function () {
  phpinfo();
});


Route::group(

    ['middleware' => ['auth.jwt']],
    function () {
        Route::group(['prefix' => 'user'], function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('/',  [AuthController::class, 'store']);
            Route::get('/', [AuthController::class, 'getAuthUser']);
            Route::get('/userinfo', [AuthController::class, 'getUserInfo']);
            Route::post('/password', [AuthController::class, 'changeAuthUserPassword']);
            Route::get('/auth/company/rol', [AuthController::class, 'getCompanyRolAuthUser']);
            Route::get('/paginated-for-report', [AuthController::class, 'getUsers']);
            Route::post('/password/{id}', [AuthController::class, 'resetAuthUserPassword']);
            Route::get('/company/{company_id}', [AuthController::class, 'getCompanyUsers']);
            Route::get('/holiday-responsible', [AuthController::class, 'isHolidayResponsible']);
            // Route::get('/auth/rol', [AuthController::class, 'getRolAuthUser']); //no existe getRolAuthUser
            Route::get('/{id}', [AuthController::class, 'getUserForUptade']);
            Route::post('/{id}', [AuthController::class, 'update']);
            Route::delete('/{id}', [AuthController::class, 'delete']);
            Route::post('/archive/{id}', [AuthController::class, 'delete']);
            Route::post('/switch/{id}', [AuthController::class, 'switchUser']);

        });

        Route::group(['prefix' => 'payrolls', "middleware" => "role:1"], function () {
            Route::post('/upload', [PayrollController::class, 'upload']);
            Route::post('/update', [PayrollController::class, 'update']);
            Route::post('/delete', [PayrollController::class, 'delete']);
        });
    }

    
);

/*

    Route::group(["prefix" => "payrolls", "middleware" => "role:1"], function () {
        Route::post('/upload', 'Api\\PayrollController@upload');
        Route::post('/update', 'Api\\PayrollController@update');
        Route::post('/delete', 'Api\\PayrollController@delete');
    });

    
*/
