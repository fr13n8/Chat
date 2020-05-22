<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'User\UserController@login');
    Route::post('registration', 'User\UserController@registration');
    Route::post('logout', 'User\UserController@logout');
    Route::post('refresh', 'User\UserController@refresh');
    Route::post('me', 'User\UserController@me');
});

// Route::post('/user/signUp', 'User\UserController@signUp');

// Route::post('/user/signIn', 'User\UserController@signIn');

Route::post('/user/checkName', 'User\UserController@checkName');

Route::post('/user/forgotPassword', 'User\UserController@forgotPassword');

Route::get('/resetPasswordCheck/{hash}/{id}', 'User\UserController@resetPassword');

Route::post('/user/changePassword', 'User\UserController@changePassword');

// Route::post('/profile/getUser', 'Profile\ProfileController@getUser');

Route::post('/profile/changeAvatar', 'Profile\ProfileController@changeAvatar');

Route::post('/profile/updateUser', 'Profile\ProfileController@updateUser');

Route::post('/messages', 'Profile\MessagesController@fetchMessages');

Route::post('/sendMessage', 'Profile\MessagesController@sendMessage');

Route::post('/addRoom', 'RoomsController@addRoom');

Route::post('/joinRoom', 'RoomsController@joinRoom');

Route::post('/leaveRoom', 'RoomsController@leaveRoom');

Route::post('/fetchRooms', 'RoomsController@fetchRooms');

Route::post('/deleteRoom', 'RoomsController@deleteRoom');