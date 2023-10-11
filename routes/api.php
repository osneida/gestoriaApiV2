<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::post('authenticate', [AuthController::class, 'login']);

//Route::post('user/password', [AuthController::class, 'changeAuthUserPassword']);
 

Route::post('forgot_password', 'Api\\AuthController@forgotPassword');
Route::post('reset_password', 'Api\\AuthController@resetPassword');
Route::get('phpinfo', function () {
    phpinfo();
});



Route::group(
    
    ['middleware' => ['auth.jwt']],
    function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::group(['prefix' => 'user'], function () {

            Route::post('/',  [AuthController::class, 'store']);
            Route::get('/', [AuthController::class, 'getAuthUser']);
            Route::get('/userinfo', [AuthController::class, 'getUserInfo']);
            Route::post('/password', [AuthController::class, 'changeAuthUserPassword']);
            Route::get('/auth/company/rol', [AuthController::class, 'getCompanyRolAuthUser']);
        });
    }
);

/*
Route::group(['middleware' => ['auth.jwt']], function () {
    Route::post('logout', 'Api\\AuthController@logout');



    Route::group(['prefix' => 'user'], function () {
         
      
        Route::post('/', 'Api\\AuthController@store');
        Route::get('/paginated-for-report', 'Api\\AuthController@getUsers');

        Route::post('/password/{id}', 'Api\\AuthController@resetAuthUserPassword');
        Route::get('/company/{company_id}', 'Api\\AuthController@getCompanyUsers');
        Route::get('/holiday-responsible', 'Api\\AuthController@isHolidayResponsible');
        Route::get('/auth/rol', 'Api\\AuthController@getRolAuthUser');
        Route::get('/auth/company/rol', 'Api\\AuthController@getCompanyRolAuthUser');
        Route::get('/{id}', 'Api\\AuthController@getUserForUptade');
        Route::post('/{id}', 'Api\\AuthController@update');
        Route::delete('/{id}', 'Api\\AuthController@delete');
        Route::post('/archive/{id}', 'Api\\AuthController@delete');
        Route::post('/switch/{id}', 'Api\\AuthController@switchUser');
      
    });*/
