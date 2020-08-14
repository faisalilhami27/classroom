<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'blank', 'middleware' => 'auth:employee'], function () {
  Route::get('/', 'ErrorPageController@blank')->name('blank.index');
});

Route::group(['prefix' => 'forbidden', 'middleware' => 'auth:employee'], function () {
  Route::get('/', 'ErrorPageController@forbidden')->name('forbidden.index');
});
