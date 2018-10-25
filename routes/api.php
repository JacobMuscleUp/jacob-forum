<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// List all articles
Route::get('articles', 'ArticleController@index');
// List {user}'s articles
Route::get('articles/{user}', 'ArticleController@indexFiltered');
// List an article
Route::get('article/{id}', 'ArticleController@show');
// Create a new article
Route::post('article', 'ArticleController@store');
// Update an article
Route::put('article', 'ArticleController@store');
// Delete an article
Route::delete('article/{id}', 'ArticleController@destroy');

Route::post('article/{id}', 'ArticleController@updateViewCount');

// List all comments attached to the article with id {articleId}
Route::get('comments/{articleId}', 'CommentController@showAll');
Route::post('comment', 'CommentController@store');
Route::put('comment', 'CommentController@store');