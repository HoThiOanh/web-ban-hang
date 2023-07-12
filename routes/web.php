<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [App\Http\Controllers\Front\HomeController::class, 'index']);

Route::get('shop/product/{id}', [App\Http\Controllers\Front\ShopController::class, 'show']);
Route::prefix('shop')->group(function (){
    //Route::get('shop/product/{id}', [App\Http\Controllers\Front\ShopController::class, 'show']);
    Route::post('shop/product/{id}', [App\Http\Controllers\Front\ShopController::class, 'postComment']);
    Route::get('', [App\Http\Controllers\Front\ShopController::class, 'index']);
    Route::get('category/{categoryName}', [App\Http\Controllers\Front\ShopController::class, 'category']);
});

Route::prefix('cart')->group(function (){
    Route::get('add', [App\Http\Controllers\Front\CartController::class, 'add']);
    Route::get('/', [App\Http\Controllers\Front\CartController::class, 'index']);
    Route::get('delete', [App\Http\Controllers\Front\CartController::class, 'delete']);
    Route::get('destroy', [App\Http\Controllers\Front\CartController::class, 'destroy']);
    Route::get('update', [App\Http\Controllers\Front\CartController::class, 'update']);

});

Route::prefix('checkout')->group(function () {
    Route::get('', [App\Http\Controllers\Front\CheckoutController::class, 'checkout']);
    Route::post('/', [App\Http\Controllers\Front\CheckoutController::class, 'addOrder']);
    Route::get('/result', [App\Http\Controllers\Front\CheckoutController::class, 'result']);

    Route::get('/vnPayCheck', [App\Http\Controllers\Front\CheckoutController::class, 'vnPayCheck']);
});


Route::prefix('account')->group(function (){
    Route::get('/login', [App\Http\Controllers\Front\AccountController::class, 'login']);
    Route::post('/login', [App\Http\Controllers\Front\AccountController::class, 'checkLogin']);

    Route::get('logout', [App\Http\Controllers\Front\AccountController::class, 'logout']);

    Route::get('register', [App\Http\Controllers\Front\AccountController::class, 'register']);
    Route::post('register', [App\Http\Controllers\Front\AccountController::class, 'postRegister']);

    Route::prefix('my-order')->middleware('CheckMemberLogin')->group(function (){
        Route::get('/', [App\Http\Controllers\Front\AccountController::class, 'myOrderIndex']);
        Route::get('{id}', [App\Http\Controllers\Front\AccountController::class, "myOrderShow"]);
    });
});

//Admin
Route::prefix('admin')->middleware('CheckAdminLogin')->group(function (){
    Route::redirect('', 'admin/user');
    Route::resource('user', \App\Http\Controllers\Admin\UserController::class);

    Route::resource('category', \App\Http\Controllers\Admin\ProductCategoryController::class);
    Route::resource('brand', \App\Http\Controllers\Admin\BrandController::class);
    Route::resource('product', \App\Http\Controllers\Admin\ProductController::class);


    Route::prefix('login')->group(function (){
        Route::get('', [App\Http\Controllers\Admin\HomeController::class, 'getLogin'])->withoutMiddleware('CheckAdminLogin');
        Route::post('' ,[App\Http\Controllers\Admin\HomeController::class, 'postLogin'])->withoutMiddleware('CheckAdminLogin');
    });
    Route::get('logout', [App\Http\Controllers\Admin\HomeController::class, 'logout']);
});
