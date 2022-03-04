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


Route::get('/', [App\Http\Controllers\ProductController::class, 'getIndex'])->name('product.index');

Route::middleware('guest')->get('/signup', [App\Http\Controllers\UserController::class, 'getSignup'])->name('user.signup');
Route::middleware('guest')->post('/signup', [App\Http\Controllers\UserController::class, 'postSignup'])->name('user.signup');
Route::middleware('guest')->get('/signin', [App\Http\Controllers\UserController::class, 'getSignin'])->name('user.signin');
Route::middleware('guest')->post('/signin', [App\Http\Controllers\UserController::class, 'postSignin'])->name('user.signin');
Route::middleware('auth')->get('/user/profile', [App\Http\Controllers\UserController::class, 'getProfile'])->name('user.profile');
Route::middleware('auth')->get('/user/logout', [App\Http\Controllers\UserController::class, 'getLogout'])->name('user.logout');

Route::get('/add-to-cart/{id}',[App\Http\Controllers\ProductController::class, 'getAddToCart'])->name('product.addToCart');
Route::middleware('auth')->get('/shopping-cart',[App\Http\Controllers\ProductController::class, 'getCart'])->name('product.shoppingCart');
Route::middleware('auth')->get('/checkout',[App\Http\Controllers\ProductController::class, 'getCheckout'])->name('checkout');
//Route::middleware('auth')->post('/checkout',[App\Http\Controllers\ProductController::class, 'postCheckout'])->name('checkout');
Route::post('/checkout-cashier',[App\Http\Controllers\ProductController::class, 'checkoutCashier']);
Route::post('/post-checkout-stripe',[App\Http\Controllers\ProductController::class, 'postCheckoutStripe']);
Route::post('/after-payment', [App\Http\Controllers\ProductController::class, 'afterPayment']);