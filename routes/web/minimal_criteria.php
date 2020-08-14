<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'minimal/completeness/criteria', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'MinimalCompletenessCriteriaController@index')->name('minimal.criteria.index');
  Route::get('/edit', 'MinimalCompletenessCriteriaController@edit')->name('minimal.criteria.edit');
  Route::post('/subject', 'MinimalCompletenessCriteriaController@getSubject')->name('minimal.criteria.subject');
  Route::post('/json', 'MinimalCompletenessCriteriaController@datatable')->name('minimal.criteria.json');
  Route::post('/create', 'MinimalCompletenessCriteriaController@store')->name('minimal.criteria.create');
  Route::put('/update', 'MinimalCompletenessCriteriaController@update')->name('minimal.criteria.update');
  Route::delete('/delete', 'MinimalCompletenessCriteriaController@destroy')->name('minimal.criteria.delete');
});
