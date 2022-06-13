<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BooksReviewController;

/**
 * @var Illuminate\Support\Facades\Route $router
 */

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


Route::get('/books', 'BooksController@index');
Route::post('/books', 'BooksController@store');
Route::post('/books/{id}/reviews', 'BooksReviewController@store');
Route::delete('/books/{bookId}/reviews/{reviewId}', 'BooksReviewController@destroy');


//$router->group(['middleware' => ['auth']], function () use ($router) {

    $router->group(['prefix' => 'book'], function () use ($router) {
        $router->get('all', [BooksController::class, 'index']);
        $router->get('detail/{id}', [BooksController::class, 'show']);
        $router->get('edit/{id}', [BooksController::class, 'edit']);
        $router->get('create', [BooksController::class, 'create']);
        $router->post('baru', [BooksController::class, 'store']);
        $router->post('update', [BooksController::class, 'update']);
        $router->delete('delete/{id}', [BooksController::class, 'destroy']);
    });

    $router->group(['prefix' => 'bookreview'], function () use ($router) {
        $router->get('all', [BooksReviewController::class, 'index']);
        $router->get('detail/{id}', [BooksReviewController::class, 'show']);
        $router->get('edit/{id}', [BooksReviewController::class, 'edit']);
        $router->get('create', [BooksReviewController::class, 'create']);
        $router->post('baru', [BooksReviewController::class, 'store']);
        $router->post('update', [BooksReviewController::class, 'update']);
        $router->delete('delete/{id}', [BooksReviewController::class, 'destroy']);
    });

