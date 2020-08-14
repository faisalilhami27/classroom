<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'major', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'MajorController@index')->name('major.index');
  Route::get('/edit', 'MajorController@edit')->name('major.edit');
  Route::post('/json', 'MajorController@datatable')->name('major.json');
  Route::post('/import', 'MajorController@import')->name('major.import');
  Route::post('/create', 'MajorController@store')->name('major.create');
  Route::put('/update', 'MajorController@update')->name('major.update');
  Route::delete('/delete', 'MajorController@destroy')->name('major.delete');
});
