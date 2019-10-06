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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => '/v1'], function () {
    Route::get('/user','UserController@index')->middleware('cors');
    Route::post('/user','UserController@store')->middleware('cors');
    Route::post('/user/login','UserController@login')->middleware('cors');
    //karaoke
    Route::get('karaoke','BarKaraokeController@index')->middleware('cors');
    Route::get('karaoke/{id}','BarKaraokeController@show')->middleware('cors');
    Route::post('karaoke','BarKaraokeController@store')->middleware('cors');
    Route::put('karaoke/{id}','BarKaraokeController@update')->middleware('cors');
    //image
    Route::get('image/{id}','ImageController@show')->middleware('cors');
    Route::post('image','ImageController@store')->middleware('cors');
    //group menu
    Route::post('group-menu','GroupMenuController@store')->middleware('cors');
    //room
    Route::get("/room","RoomBarKaraokeController@index")->middleware('cors');
    Route::post("/room","RoomBarKaraokeController@store")->middleware('cors');
    Route::get('/room/{id}',"RoomBarKaraokeController@show")->middleware('cors');
    Route::put("/room/{id}","RoomBarKaraokeController@update")->middleware('cors');
    //attribure
    Route::get('/attribute-room', 'AttributeRoomController@index')->middleware('cors');
    Route::post('/attribute-room', 'AttributeRoomController@store')->middleware('cors');
    Route::put('/attribute-room/{id}', 'AttributeRoomController@update')->middleware('cors');
    Route::delete('/attribute-room/{id}', 'AttributeRoomController@destroy')->middleware('cors');
    //province 
    Route::get('province','ProvinceController@index')->middleware('cors');
    //district
    Route::get('district','DistrictController@index')->middleware('cors');
});