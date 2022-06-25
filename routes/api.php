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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('my-package', 'PackageController@indexApi')->name('Package.indexApi');

//Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
//    Route::get('products/{id?}', 'ProductController@getProductsApi');
////    Route::get('categories', 'CategoryController@getCategoriesApi');
//    Route::get('brands', 'BrandController@getBrandsApi');
//    Route::post('customers', 'ContactController@postCustomersApi');
//    Route::get('settings', 'BusinessController@getEcomSettings');
//    Route::get('variations', 'ProductController@getVariationsApi');
//    Route::post('orders', 'SellPosController@placeOrdersApi');
//});
