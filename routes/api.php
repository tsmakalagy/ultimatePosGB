<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
//Route::middleware('api')->group(function () {
//    Route::get('my-products', 'ProductController@indexApi')->name('Product.indexApi');
//});

Route::group(['middleware' => ['web']], function(){
    Route::get('my-products/{sku}', 'ProductController@indexApi')->name('Product.indexApi');
    Route::get('all-products/', 'ProductController@allProductsApi')->name('Product.allProductsApi');
    Route::get('down-products/', 'ImportProductsController@exportToCSV')->name('Product.downProductsApi');
});




//Route::get('my-products', 'ProductController@indexApi')->name('Product.indexApi');
