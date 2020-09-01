<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@store')->name('register.create');
Route::get('/verify/email/{token}', 'Auth\RegisterController@emailVerification')->name('register.verification');
Route::get('/', 'DashboardController@index')->middleware('auth:employee')->name('dashboard');
Route::get('/home', 'HomeController@index')
  ->middleware('auth:student,employee')
  ->name('home');
Route::get('/detail/{id}/{subject}', 'HomeController@classPage')
  ->middleware('auth:student,employee')
  ->name('detail')
  ->where(['id', 'subject'],'.*');
Route::get('/chat/page', 'HomeController@index')
  ->middleware('auth:student,employee')
  ->name('chat')
  ->where(['id', 'subject'],'.*');
Route::get('/task/detail/{id}/{posting_id}', 'HomeController@taskPage')
  ->middleware('auth:student,employee')
  ->name('detail.task')
  ->where(['id'],'.*');
Route::get('/posting/comment/{id}', 'HomeController@index')
  ->middleware('auth:student,employee')
  ->name('detail.task')
  ->where(['id'],'.*');
Route::get('/exam/page/{id}', 'HomeController@examPage')
  ->middleware('auth:student')
  ->name('exam.page')
  ->where(['id'],'.*');
Route::get('/exam/detail/{id}', 'HomeController@index')
  ->middleware('auth:employee')
  ->name('exam.detail')
  ->where(['id'],'.*');
Route::post('/choose/role', 'RoleController@pickRole')
  ->middleware('auth:employee')
  ->name('role.pick');
Route::get('/choose/roles', 'RoleController@chooseRole')
  ->middleware('auth:employee')
  ->name('role.pickList');

require 'web/announcement.php';
require 'web/employee.php';
require 'web/comment.php';
require 'web/profile.php';
require 'web/video_conference.php';
require 'web/assessment.php';
require 'web/student.php';
require 'web/chat.php';
require 'web/task.php';
require 'web/e-learning.php';
require 'web/user_employee.php';
require 'web/user_student.php';
require 'web/role.php';
require 'web/major.php';
require 'web/posting.php';
require 'web/cbt.php';
require 'web/student_class.php';
require 'web/subject.php';
require 'web/school_year.php';
require 'web/minimal_criteria.php';
require 'web/grade_level.php';
require 'web/semester.php';
require 'web/navigation.php';
require 'web/permission.php';
require 'web/errors.php';
require 'web/configuration.php';
