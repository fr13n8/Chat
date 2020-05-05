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

Route::get('/user/verify/{hash}/{id}', 'User\UserController@userVerify');
// Route::get('/user/resetPassword/{hash}/{id}', 'User\UserController@resetPassword');
Route::get('/', 'MainController@mainPage');
Route::get('/{any}', function(){
                                return view('mainPage');
                            })->where('any', ".*");
