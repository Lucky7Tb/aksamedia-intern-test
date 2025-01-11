<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'welcome!',
    ], Response::HTTP_OK);
});

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    Route::get('divisions', \App\Http\Controllers\DivisionController::class);

    Route::prefix('employees')->group(function () {
        Route::get('/', [\App\Http\Controllers\EmployeeController::class, 'getAll']);
        Route::post('/', [\App\Http\Controllers\EmployeeController::class, 'create']);
        Route::post('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update']);
        Route::delete('/{employee}', [\App\Http\Controllers\EmployeeController::class, 'delete']);
    });
});

Route::get('nilaiRT', [\App\Http\Controllers\NilaiController::class, 'nilaiRT']);
Route::get('nilaiST', [\App\Http\Controllers\NilaiController::class, 'nilaiST']);

Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'route not found',
    ], Response::HTTP_NOT_FOUND);
});
