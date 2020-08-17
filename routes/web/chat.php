<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'chat', 'middleware' => ['auth:employee,student']], function () {
  Route::post('/check/user', 'ChatRoomController@checkTeacherOrStudent')->name('chat.check');
  Route::post('/get/person', 'ChatRoomController@getTeacherOrStudent')->name('chat.get.person');
  Route::post('/get', 'ChatRoomController@getChat')->name('chat.get');
  Route::post('/list', 'ChatRoomController@listChat')->name('chat.list');
  Route::post('/send', 'ChatRoomController@makeOrReplyChat')->name('chat.send');
});
