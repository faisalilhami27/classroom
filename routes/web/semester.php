<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'semester', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'SemesterController@index')->name('semester.index');
  Route::get('/edit', 'SemesterController@edit')->name('semester.edit');
  Route::post('/json', 'SemesterController@datatable')->name('semester.json');
  Route::post('/create', 'SemesterController@store')->name('semester.create');
  Route::put('/update', 'SemesterController@update')->name('semester.update');
  Route::delete('/delete', 'SemesterController@destroy')->name('semester.delete');
});
