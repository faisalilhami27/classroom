<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'school/year', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'SchoolYearController@index')->name('school.year.index');
  Route::get('/edit', 'SchoolYearController@edit')->name('school.year.edit');
  Route::post('/json', 'SchoolYearController@datatable')->name('school.year.json');
  Route::post('/create', 'SchoolYearController@store')->name('school.year.create');
  Route::put('/update', 'SchoolYearController@update')->name('school.year.update');
  Route::put('/change/status', 'SchoolYearController@changeStatus')->name('school.year.change');
  Route::delete('/delete', 'SchoolYearController@destroy')->name('school.year.delete');
});
