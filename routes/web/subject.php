<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'subject', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'SubjectController@index')->name('subject.index');
  Route::get('/edit', 'SubjectController@edit')->name('subject.edit');
  Route::post('/json', 'SubjectController@datatable')->name('subject.json');
  Route::post('/create', 'SubjectController@store')->name('subject.create');
  Route::post('/import', 'SubjectController@import')->name('subject.import');
  Route::put('/update', 'SubjectController@update')->name('subject.update');
  Route::delete('/delete', 'SubjectController@destroy')->name('subject.delete');
});
