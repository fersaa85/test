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

Route::get('/divisa', 'DivisaController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('posts')->group(function () {
    Route::get('/', 'PostController@index')->name('lists');
    Route::get('/show/{id}', 'PostController@show')->name('show');
    Route::get('/create', 'PostController@create')->name('create');
    Route::post('/store', 'PostController@store')->name('store');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
