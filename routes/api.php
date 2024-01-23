<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;


Route::get('/categories', [CategoryController::class,'index']);
Route::get('/category/{url}', [CategoryController::class,'show']);
Route::put('/category/{url}', [CategoryController::class,'update']);
Route::delete('/category/{url}', [CategoryController::class,'destroy']);
Route::post('/category', [CategoryController::class,'store']);

Route::get('/companies', [CompanyController::class,'index']);
Route::post('/company', [CompanyController::class,'store']);
Route::get('/company/{uuid}', [CompanyController::class,'show']);
Route::put('/company/{uuid}', [CompanyController::class,'update']);
Route::delete('/company/{uuid}', [CompanyController::class,'destroy']);
Route::get('/company', [CompanyController::class,'search']);


Route::get('/', function () {
    return response()->json(['message' => 'success']);
});


