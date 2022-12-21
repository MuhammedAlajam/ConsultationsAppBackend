<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

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

// Public Routes
Route::post('users/register', [UserController::class, 'register']);
Route::post('experts/register', [ExpertController::class, 'register']);
Route::post('login',[UserController::class,'login']);

//Protected Routes

Route::group(['middleware'=>['auth:sanctum']],function () {

    Route::put('experts/rate/{id}', [ExpertController::class, 'rate']);
    Route::put('users/transfair', [UserController::class, 'transfairMoney']);
    
    Route::get('users/favorites/{id}', [UserController::class, 'favorites']);
    Route::post('users/addFavorite', [UserController::class,  'addFavorite']);
    
    Route::get('experts/all', [ExpertController::class, 'showAll']);
    Route::get('experts/show/{id}',[ExpertController::class,'show']);
    Route::get('experts/searchByName/{name}', [ExpertController::class, 'searchByName']);
    Route::get('experts/searchByConsultation/{name}', [ExpertController::class, 'searchByConsultation']);
    Route::get('experts/getBookedTimes/{id}', [ExpertController::class, 'getBookedTimes']);
});