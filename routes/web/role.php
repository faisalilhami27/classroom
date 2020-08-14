<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'role', 'middleware' => ['auth:employee', 'roles:developer']], function () {
  Route::get('/', 'RoleController@index')->name('role.index');
  Route::get('/edit', 'RoleController@edit')->name('role.edit');
  Route::post('/json', 'RoleController@datatable')->name('role.json');
  Route::post('/create', 'RoleController@store')->name('role.create');
  Route::put('/update', 'RoleController@update')->name('role.update');
  Route::delete('/delete', 'RoleController@destroy')->name('role.delete');
});
