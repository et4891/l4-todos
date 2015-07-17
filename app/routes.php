<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//print App::environment();

//	$todo = Todo::create(array(
//		'todoText' => '16th Complete Database Test',
//		'done' => 0
//	));
//	$todo->save();
Route::POST('add','TodoController@add');
Route::POST('search','TodoController@search');

Route::controller('/','TodoController');