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

// Route::middleware('auth.api:api')->get('/user', function (Request $request) {
//     return $request->user();

// });

Route::group(['prefix'=>'auth'],
function(){
    Route::post('register' ,'AuthController@register');
    Route::post('login' ,'AuthController@login');
    // Route::get('logout' ,'AuthController@logout')->middleware('auth.api:api');
    
}
);


Route::middleware([ 'auth.api:api'])->group(function () {
    // Route::get('my-package', 'PackageController@indexApi')->name('Package.indexApi');
    Route::get('product', 'ProductController@indexApi')->name('Product.indexApi');
    Route::post('shipment-status' ,'SellController@shipmentStatus')->name('ShipmentStatus.api');
    Route::get('logout' ,'AuthController@logout')->name('Logout.api');
    
});
// Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
//     Route::get('my-package', 'PackageController@indexApi')->name('Package.indexApi');
//     Route::get('product', 'ProductController@indexApi')->name('Product.indexApi');
// });


