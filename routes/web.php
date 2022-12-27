<?php

use App\Models\PurchaseOrderType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\StoreApiController;
use App\Http\Controllers\Admin\GrnCrudController;
use App\Http\Controllers\Api\suporgApiController;
use App\Http\Controllers\Auth\UserCrudController;
use App\Http\Controllers\Api\DistrictApiController;
use App\Http\Controllers\Api\ProvinceApiController;
use App\Http\Controllers\Api\DependentDropdownController;

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
    return redirect('/admin/dashboard');
});
Route::get('/home', function () {
    return redirect('/admin/dashboard');
});


// Route::get('admin/change_status/{btn_id}', 'App\Http\Controllers\AdminController@changeTabStatus');
Route::get('api/district/{province_id}', [DistrictApiController::class, 'index']);
Route::get('api/province/{country_id}', [ProvinceApiController::class, 'index']);
Route::get('admin/fetch_employee_detail', [UserCrudController::class, 'fetchEmployeeDetails']);
Route::get('api/store_id/{sup_org_id}', [StoreApiController::class, 'index']);
Route::get('api/sup_org_id/{store_id}', [suporgApiController::class, 'index']);

Route::get('admin/api/department/{depart_id}', [DependentDropdownController::class, 'getSubDepartment']);

Route::get('/menu_search/action', [AdminController::class, 'action'])->name('menu_search.action');

Route::get('admin/dashboard', [AdminController::class, 'dashboardHome']);

Route::get('api/getprovince/{country_id}', [DependentDropdownController::class, 'getProvince']);
Route::get('api/getdistrict/{province_id}', [DependentDropdownController::class, 'getDistrict']);

Route::get('api/getStore/{sup_org_id}', [DependentDropdownController::class, 'getStore']);

//Fetch Super Data to Other Organizations
Route::post('fetch-super-data/{modelPath}', [AdminController::class, 'fetchMasterData'])->name('fetch.superData');

