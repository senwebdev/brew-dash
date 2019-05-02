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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/show-batch', 'BatchController@showBatch');
Route::post('/update-batch/{tank_id}/{amount}/{direction}', 'BatchController@updateBatch');
Route::post('/move-batch/{source_id}/{target_id}/{move_amount}', 'BatchController@moveBatch');
Route::post('/dump-batch/{tank_id}', 'BatchController@dumpBatch');
Route::post('/keg-batch/{tank_id}', 'BatchController@kegBatch');