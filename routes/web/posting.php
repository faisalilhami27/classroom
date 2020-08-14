<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'posting', 'middleware' => ['auth:employee,student']], function () {
  Route::get('/get', 'PostingController@getDataPosting')->name('posting.get');
  Route::get('/get/task', 'PostingController@getClosestTask')->name('posting.task');
  Route::get('/edit', 'PostingController@edit')->name('posting.edit');
  Route::get('/show', 'PostingController@show')->name('posting.show');
  Route::post('/create', 'PostingController@store')->name('posting.create');
  Route::post('/update', 'PostingController@update')->name('posting.update');
  Route::delete('/delete', 'PostingController@destroy')->name('posting.delete');
});
