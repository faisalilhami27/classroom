<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'navigation', 'middleware' => ['auth:employee', 'roles:developer']], function () {
  Route::get('/', 'NavigationController@index')->name('navigation.index');
  Route::get('/edit', 'NavigationController@edit')->name('navigation.edit');
  Route::post('/json', 'NavigationController@datatable')->name('navigation.json');
  Route::post('/create', 'NavigationController@store')->name('navigation.create');
  Route::put('/update', 'NavigationController@update')->name('navigation.update');
  Route::delete('/delete', 'NavigationController@destroy')->name('navigation.delete');
});
