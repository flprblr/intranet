<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\AssurantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\PosInformationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ExtGuaranteeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MvAccessController;
use App\Http\Controllers\MvWarehouseController;
use App\Http\Controllers\MvOrderController;
use App\Http\Controllers\MvStoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/user', UserController::class);
    Route::resource('/role', RoleController::class);
    Route::resource('/permission', PermissionController::class);
    Route::resource('/state', StateController::class);
    Route::resource('/city', CityController::class);
    Route::resource('/store', StoreController::class);
    Route::resource('/station', StationController::class);
    Route::resource('/service', ServiceController::class);
    Route::resource('/company', CompanyController::class);
    Route::resource('/ecommerce', EcommerceController::class);
    Route::resource('/order', OrderController::class);
    Route::resource('/extguarantee', ExtGuaranteeController::class);
    Route::get('/getdatainvoice/{nro_documento}', [ExtGuaranteeController::class, 'getInvoiceData'])->name('getInvoiceData');
    Route::get('/getdatainvoicegext/{nro_documento}', [ExtGuaranteeController::class, 'getInvoiceDataGext'])->name('getInvoiceDataGext');
    Route::get('/getsellername/{sellercode}', [ExtGuaranteeController::class, 'getSellerName'])->name('getSellerName');
    Route::post('/send-email/{nro_documento}', [ExtGuaranteeController::class, 'sendEmail']);

    Route::get('/sales', [SalesController::class, 'index'])->name('sales');
    Route::get('/sales/retail', [SalesController::class, 'retail'])->name('sales-retail');
    Route::get('/sales/ecommerce', [SalesController::class, 'ecommerce'])->name('sales-ecommerce');
    Route::get('/sales/marketplace', [SalesController::class, 'marketplace'])->name('sales-marketplace');
    Route::get('/sales/wholesale', [SalesController::class, 'wholesale'])->name('sales-wholesale');
    Route::get('/sales/belsport', [SalesController::class, 'belsport'])->name('sales-belsport');
    Route::get('/sales/bold', [SalesController::class, 'bold'])->name('sales-bold');
    Route::get('/sales/k1', [SalesController::class, 'k1'])->name('sales-k1');
    Route::get('/sales/drops', [SalesController::class, 'drops'])->name('sales-drops');
    Route::get('/sales/outlets', [SalesController::class, 'outlets'])->name('sales-outlets');
    Route::get('/sales/locker', [SalesController::class, 'locker'])->name('sales-locker');
    Route::get('/sales/qsrx', [SalesController::class, 'qsrx'])->name('sales-qsrx');
    Route::get('/sales/antihuman', [SalesController::class, 'antihuman'])->name('sales-antihuman');
    Route::get('/sales/saucony', [SalesController::class, 'saucony'])->name('sales-saucony');
    Route::get('/sales/aufbau', [SalesController::class, 'aufbau'])->name('sales-aufbau');
    Route::get('/sales/bamers', [SalesController::class, 'bamers'])->name('sales-bamers');
    Route::get('/sales/crocs', [SalesController::class, 'crocs'])->name('sales-crocs');
    Route::get('/sales/oakley', [SalesController::class, 'oakley'])->name('sales-oakley');
    Route::get('/sales/thelab', [SalesController::class, 'thelab'])->name('sales-thelab');
    Route::get('/sales/hoka', [SalesController::class, 'hoka'])->name('sales-hoka');
    Route::resource('/assurant', AssurantController::class);
    Route::post('/assurant', [AssurantController::class, 'search']);
    Route::get('/sales/pos', [PosInformationController::class, 'index'])->name('report-pos');
    Route::post('/sales/pos', [PosInformationController::class, 'search']);
    Route::resource('/manager', ManagerController::class);

    
    Route::resource('/mvaccess', MvAccessController::class);
    Route::resource('/mvwarehouse', MvWarehouseController::class);
    Route::resource('/mvorder', MvOrderController::class);
    Route::resource('/mvstore', MvStoreController::class);
});
