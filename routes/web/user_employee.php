<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/employee', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'UserEmployeeController@index')->name('user.employee.index');
  Route::get('/edit', 'UserEmployeeController@edit')->name('user.employee.edit');
  Route::get('/username', 'UserEmployeeController@checkUsername')->name('user.employee.username');
  Route::post('/get/employee', 'UserEmployeeController@getUnregisteredEmployees')->name('user.employee.get');
  Route::post('/json', 'UserEmployeeController@datatable')->name('user.employee.json');
  Route::post('/reset', 'UserEmployeeController@resetPassword')->name('user.employee.reset');
  Route::post('/create', 'UserEmployeeController@store')->name('user.employee.create');
  Route::put('/update', 'UserEmployeeController@update')->name('user.employee.update');
  Route::delete('/delete', 'UserEmployeeController@destroy')->name('user.employee.delete');
});
