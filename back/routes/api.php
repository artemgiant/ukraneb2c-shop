<?php

use App\Http\Controllers\Api\Address\AddressController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Product\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::match(['get','post'],'products',[ProductController::class,'index']);

Route::get('orders/create',[OrderController::class,'create']);
Route::post('orders/create',[OrderController::class,'create']);

Route::group(['prefix' => 'addresses/search'], function () {
    Route::post('city', [AddressController::class,'searchCity'])->name('addresses.search.city');
    Route::post('street', [AddressController::class,'searchStreet'])->name('addresses.search.street');
    Route::post('warehouse', [AddressController::class,'searchWarehouse'])->name('addresses.search.warehouse');
//        Route::get('warehouses', 'DirectoryAddresses\SearchController@searchWarehouse');
//        Route::get('warehouses/{id}', 'DirectoryAddresses\SearchController@getWarehouse');
});
