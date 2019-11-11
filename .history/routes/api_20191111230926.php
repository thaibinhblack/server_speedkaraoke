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
    Route::post('/facebook','UserController@facebook')->middleware('cors');
    Route::post('/google','UserController@google')->middleware('cors');
    Route::get('/user','UserController@index')->middleware('cors');
    Route::get('/user/{id}/manager-karaoke','UserController@manager')->middleware('cors');
    Route::post('/user/{id}/update','UserController@update')->middleware('cors');
    Route::get('/info/user/{id}','UserController@show')->middleware('cors');
    Route::get('/user/token','UserController@checkToken')->middleware('cors');
    Route::post('/user','UserController@store')->middleware('cors');
    Route::post('/resignter','UserController@resignter')->middleware('cors');
    Route::post('/user/login','UserController@login')->middleware('cors');
    //karaoke
    Route::get('/map','BarKaraokeController@map')->middleware('cors');
    Route::get('karaoke','BarKaraokeController@index')->middleware('cors');
    Route::get('karaoke/{id}','BarKaraokeController@show')->middleware('cors');
    Route::get('karaoke/{id}/rating','BarKaraokeController@rating')->middleware('cors');
    Route::post('karaoke/{id}/update','BarKaraokeController@update')->middleware('cors');
    Route::get('karaoke/{id}/view','BarKaraokeController@view')->middleware('cors');
    Route::post('karaoke','BarKaraokeController@store')->middleware('cors');
    Route::put('karaoke/{id}','BarKaraokeController@update')->middleware('cors');
    //comment karaoke
    Route::get('comment/{id}','CommentKaraokeController@show')->middleware('cors');
    Route::post('comment','CommentKaraokeController@store')->middleware('cors');
    //comment room
    Route::get('comment_room/{id}','CommentRoomController@show')->middleware('cors');
    Route::post('comment_room','CommentRoomController@store')->middleware('cors');
    //image
    Route::get('image/{id}','ImageController@show')->middleware('cors');
    Route::post('image/upload','ImageController@upload')->middleware('cors');
    Route::post('image','ImageController@store')->middleware('cors');
    //group menu
    Route::post('group-menu','GroupMenuController@store')->middleware('cors');
    //room
    Route::get("/room","RoomBarKaraokeController@index")->middleware('cors');
    Route::post("/room","RoomBarKaraokeController@store")->middleware('cors');
    Route::get('/room/{id}',"RoomBarKaraokeController@show")->middleware('cors');
    Route::get('/room/{id}/rating',"RoomBarKaraokeController@rating")->middleware('cors');
    Route::post("/room/{id}","RoomBarKaraokeController@update")->middleware('cors');
    //attribure
    Route::get('/attribute-room', 'AttributeRoomController@index')->middleware('cors');
    Route::post('/attribute-room', 'AttributeRoomController@store')->middleware('cors');
    Route::post('/attribute-room/{id}', 'AttributeRoomController@update')->middleware('cors');
    Route::post('/attribute-room/{id}/delete', 'AttributeRoomController@destroy')->middleware('cors');
    //province 
    Route::get('province','ProvinceController@index')->middleware('cors');
    Route::post('province/search','ProvinceController@search')->middleware('cors');
    Route::get('province/{id}','ProvinceController@show')->middleware('cors');
    //district
    Route::get('district','DistrictController@index')->middleware('cors');
    Route::get('district/{id}','DistrictController@show')->middleware('cors');
    //group function
    Route::get("/group","GroupFunctionController@index")->middleware('cors');

    //manager
    Route::get('/manager','ManagerKaraokeController@index')->middleware('cors');
    Route::get('/manager/{id}','ManagerKaraokeController@show')->middleware('cors');

    //rule
    Route::get('/rule','RuleController@index')->middleware('cors');
    Route::post('/rule','RuleController@store')->middleware('cors');

    //booking
    Route::get('cancle','BookingController@cancle')->middleware('cors');
    Route::post('bookingmobile','BookingController@bookingmobile')->middleware('cors');
    Route::get('check_booking','BookingController@checkmobile')->middleware('cors');
    Route::get('/booking','BookingController@index')->middleware('cors');
    Route::get('/booking/{id}/check','BookingController@check')->middleware('cors');
    Route::get('/booking/{id}/show','BookingController@show')->middleware('cors');
    Route::get('/booking/{id}/paypal','BookingController@paypal')->middleware('cors');
    Route::get('/booking/{id}/booking','BookingController@booking')->middleware('cors');
    Route::post('/booking','BookingController@store')->middleware('cors');

    //promotion
    Route::get('/promotion','PromotionController@index')->middleware('cors');
    Route::get('/promotion/{id}','PromotionController@show')->middleware('cors');
    Route::get('/promotion/{id}/check','PromotionController@check_promotion')->middleware('cors');
    Route::get('/promotion/{id}/karaoke','PromotionController@karaoke')->middleware('cors');
    Route::post('/promotion','PromotionController@store')->middleware('cors');

    //user view kararoke
    Route::post('/view_karaoke','ViewKaraokeController@store')->middleware('cors');

    //like mobile
    Route::get('like_karaoke', 'LikeKaraokeMobile@index')->middleware('cors');
    Route::post('like_mobile', 'LikeKaraokeMobile@store')->middleware('cors');

    //SMS
    Route::get('/sms','SMSController@index')->middleware('cors');
});