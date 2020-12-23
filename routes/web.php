<?php

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


Route::get('/', 'ToDoController@showAllIncompleteToDos');
Route::post('/add', 'ToDoController@addToDo')->name('add');
Route::post('/delete_to_do', 'ToDoController@deleteToDo')->name('delete_to_do');
Route::post('/get_details', 'ToDoController@getDetails')->name('get_details');
Route::post('/update_as_done', 'ToDoController@updateToDoAsDone')->name('update_as_done');
Route::post('/validate_duplicacy', 'ToDoController@validateDuplicacy')->name('validate_duplicacy');