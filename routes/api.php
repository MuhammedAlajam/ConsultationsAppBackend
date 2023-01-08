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



//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::put('experts/rate/{id}', [ExpertController::class, 'rate']);
    Route::put('users/transfair', [UserController::class, 'transfairMoney']);
    Route::get('consultations/all', [UserController::class, 'consultations']);
    Route::get('experts/searchByConsultation/{id}', [ExpertController::class, 'searchByConsultation']);
    Route::get('experts/search/{name}', [ExpertController::class, 'searchByName']);

    Route::put('users/flip_favorite', [UserController::class, 'flip_favorite']);
    Route::get('favorites', [UserController::class, 'favorites']);

    // appointments stuff
    Route::post('experts/setAvailableTimes', [ExpertController::class, 'setAvailableTimes']);
    Route::post('experts/book', [ExpertController::class, 'book']);
    Route::get('experts/getAvailableTimes/{expert_id}/{s_date}', [ExpertController::class, 'getAvailableTimes']);
    Route::get('users/getUserBookedTimes', [UserController::class, 'getUserBookedTimes']);
    Route::get('experts/getExpertBookedTimes', [ExpertController::class, 'getExpertBookedTimes']);

    Route::post('users/setPhoto',[UserController::class,'setPhoto']);
    Route::get('users/getPhoto',[UserController::class,'getPhoto']);
    Route::get('users/getPhotoById/{id}',[UserController::class,'getPhotoById']);

    Route::get('users/wallet',[UserController::class,'wallet']);

    Route::get('experts/all', [ExpertController::class, 'showAll']);
    Route::get('experts/show/{id}', [ExpertController::class, 'show']);
    Route::get('experts/search/filterByConsultation/{consultation_id}/{name}', [ExpertController::class, 'filterByConsultation']);
});
