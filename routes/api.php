<?php

use App\Http\Controllers\PlayerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working'], 200);
});

Route::prefix('players')->group(function () {
    Route::get('/', [PlayerController::class, 'index']);
    Route::post('/', [PlayerController::class, 'store']);
    Route::get('/{id}', [PlayerController::class, 'show']);
    Route::put('/{id}', [PlayerController::class, 'update']);
    Route::delete('/{id}', [PlayerController::class, 'destroy']);
    Route::post('/sort', [PlayerController::class, 'sortTeams']);
});
