<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'employee', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'EmployeeController@index')->name('employee.index');
  Route::get('/json', 'EmployeeController@getDataEmployee')->name('employee.json');
  Route::get('/check/email', 'EmployeeController@checkEmail')->name('employee.email');
  Route::get('/check/phone/number', 'EmployeeController@checkPhoneNumber')->name('employee.phone');
  Route::get('/edit', 'EmployeeController@edit')->name('employee.edit');
  Route::post('/import', 'EmployeeController@import')->name('employee.import');
  Route::post('/create', 'EmployeeController@store')->name('employee.create');
  Route::put('/update', 'EmployeeController@update')->name('employee.update');
  Route::delete('/delete', 'EmployeeController@destroy')->name('employee.delete');
});
