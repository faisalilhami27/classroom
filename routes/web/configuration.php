<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'configuration', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'ConfigurationController@index')->name('configuration.index');
  Route::post('/store', 'ConfigurationController@store')->name('configuration.store');
});
