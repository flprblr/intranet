<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/stores', function () {
        $stores = DB::table('stores')
                ->select('stores.id',
                         'stores.store_number',
                         'stores.description',
                         'stores.werks',
                         'stores.extension', 
                         'stores.phone_number', 
                         'stores.gateway_ip',
                         'stores.router_ip',
                         'stores.phone_ip',
                         'stores.clock_ip',
                         'stores.wifi_password',
                         'stores.service_code',
                         'stores.address',
                         'stores.email',
                         'stores.condition',
                         'states.name as state',
                         'cities.name as city')
                ->join('states','stores.state_id','=','states.id')
                ->join('cities','stores.city_id','=','cities.id')
                ->get();
        return $stores;
    });
    Route::get('/managers', function () {
        $stores = DB::table('managers')
                ->select('managers.id',
                         'managers.name',
                         'managers.email',
                         'stores.werks',
                         'managers.store_id',)
                ->join('stores','stores.id','=','managers.store_id')
                ->orderBy('stores.id')
                ->get();
        return $stores;
    });
});

Route::get('/salescard/{society}/{salesOrg}/{channel}', [APIController::class,'salesCard']);
Route::get('/saleschartday/{society}/{salesOrg}/{channel}', [APIController::class,'salesChartDay']);
Route::get('/saleschartmonth/{society}/{salesOrg}/{channel}', [APIController::class,'salesChartMonth']);
Route::get('/salesranking/{society}/{salesOrg}/{channel}', [APIController::class,'salesRanking']);

