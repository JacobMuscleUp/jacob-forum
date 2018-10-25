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

/*
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
*/

Auth::routes();

Route::get('/', 'NavbarController@welcome')->name('welcome');
Route::get('/home', 'NavbarController@home')->name('home');
Route::get('/my', 'NavbarController@my')->name('my');
