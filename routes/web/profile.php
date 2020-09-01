<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'profile', 'middleware' => ['auth:student,employee']], function () {
  Route::get('/', 'ProfileController@index')->name('profile.index');
  Route::get('/get/user', 'ProfileController@getUserData')->name('profile.get.user');
  Route::post('/update/user', 'ProfileController@updateUserProfile')->name('profile.update.user');
  Route::put('/reset', 'ProfileController@resetPassword')->name('profile.reset');
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth:student,employee']], function () {
  Route::get('/get/student', 'ProfileController@getUserData')->name('profile.get.user');
  Route::post('/update/student', 'ProfileController@updateUserProfile')->name('profile.user');
});
