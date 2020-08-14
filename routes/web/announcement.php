<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'announcement', 'middleware' => ['auth:employee,student']], function () {
  Route::post('/get', 'AnnouncementController@getAnnouncement')->name('announcement.get');
  Route::post('/update', 'AnnouncementController@updateStatus')->name('announcement.update');
});
