<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'permission', 'middleware' => ['auth:employee', 'roles:developer']], function () {
  Route::get('/', 'UserNavigationController@index')->name('permission.index');
  Route::get('/json', 'UserNavigationController@datatable')->name('permission.json');
  Route::post('/store', 'UserNavigationController@store')->name('permission.store');
  Route::post('/update', 'UserNavigationController@update')->name('permission.update');
  Route::delete('/delete', 'UserNavigationController@destroy')->name('permission.delete');
});
