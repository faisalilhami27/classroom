<?php


use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'zoom', 'middleware' => ['auth:employee', 'roles:developer|guru']], function () {
  Route::get('/generate/user', 'VideoConferenceController@index')->name('zoom.generate.index');
  Route::post('/get/meeting', 'VideoConferenceController@getMeeting')->name('zoom.get');
  Route::post('/update', 'VideoConferenceController@generate')->name('zoom.generate');
  Route::post('/create/meeting', 'VideoConferenceController@createMeeting')->name('zoom.create');
  Route::delete('/delete/meeting', 'VideoConferenceController@deleteMeeting')->name('zoom.delete');
});

Route::group(['prefix' => 'zoom', 'middleware' => ['auth:employee,student']], function () {
  Route::post('/get/meeting', 'VideoConferenceController@getMeeting')->name('zoom.get');
});

Route::group(['prefix' => 'meeting', 'middleware' => ['auth:employee', 'roles:developer|administrator']], function () {
  Route::get('/', 'MeetingController@index')->name('meeting.index');
  Route::post('/json', 'MeetingController@datatable')->name('meeting.json');
  Route::post('/regenerate', 'MeetingController@regenerateMeeting')->name('meeting.regenerate');
});
