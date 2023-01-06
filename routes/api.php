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
Route::post('login', [UserController::class, 'login']);
Route::get('consultations/all', [UserController::class, 'consultations']);
Route::get('experts/searchByConsultation/{id}', [ExpertController::class, 'searchByConsultation']);
Route::get('experts/search/{name}', [ExpertController::class, 'searchByName']);
Route::put('experts/unrate',[ExpertController::class,'unrate']);


//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::put('experts/rate', [ExpertController::class, 'rate']);
    Route::put('users/transfair', [UserController::class, 'transfairMoney']);
    
    Route::put('users/flip_favorite',[UserController::class,'flip_favorite']);
    Route::get('favorites', [UserController::class, 'favorites']);
    Route::post('users/addFavorite', [UserController::class,  'addFavorite']);
    
    Route::post('experts/book',[ExpertController::class,'book']);
    Route::post('experts/setAvailableTimes',[ExpertController::class,'setAvailableTimes']);
    Route::get('experts/getAvailableTimes',[ExpertController::class,'getAvailableTimes']);
    Route::get('users/getUserBookedTimes',[UserController::class,'getUserBookedTimes']);
    Route::get('experts/getExpertBookedTimes',[ExpertController::class,'getExpertBookedTimes']);
    Route::get('experts/all', [ExpertController::class, 'showAll']);
    Route::get('experts/show/{id}', [ExpertController::class, 'show']);
    Route::get('experts/getBookedTimes/{id}', [ExpertController::class, 'getBookedTimes']);
    Route::get('experts/search/filterByConsultation/{name}',[ExpertController::class,'filterByConsultation']);
});
