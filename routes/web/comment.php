<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'comment', 'middleware' => ['auth:employee,student']], function () {
  Route::get('/get', 'ForumCommentController@getDataComment')->name('comment.get');
  Route::post('/add', 'ForumCommentController@addComment')->name('comment.add');
});
