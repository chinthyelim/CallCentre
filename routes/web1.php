<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');
Route::get('/questionnaire', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/submissions', 'SubmissionController@index');
Route::post('/submissions/auto', 'SubmissionController@auto');
Route::post('/submissions/list', 'SubmissionController@list');
Route::post('/submissions/list3', 'SubmissionController@list')->name('download-submissions');
Route::post('/submissions/insert', 'SubmissionController@insert');
Route::get('/submissions/pdf/{ticket_number}', 'SubmissionController@loadSubmissionsPDF');
