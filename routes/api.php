<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\BillsplitterController;
use App\Http\Controllers\GroupMembersController;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/addfriend', [FriendsController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/searchfriend', [FriendsController::class, 'search']);
    Route::get('/friends', [FriendsController::class, 'index']);
    Route::post('/addbill', [BillsplitterController::class, 'store']);
    Route::post('/addgroup', [GroupController::class, 'store']);
    Route::post('/bills', [BillsplitterController::class, 'index']);
    Route::get('/groups', [GroupController::class, 'index']);
    Route::post('/members', [GroupMembersController::class, 'members']);
    Route::get('/allusers', [FriendsController::class, 'alluser']);

});