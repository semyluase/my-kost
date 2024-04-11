<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodSnackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Utils\DropdownController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::get('/masters', [MasterController::class, 'index']);

    Route::get('/masters/homes/get-all-data', [HomeController::class, 'getAllData']);
    Route::get('/masters/homes/upload-picture/{home}', [HomeController::class, 'getDataUpload']);
    Route::get('/masters/homes/view-picture/{home}', [HomeController::class, 'showPicture']);
    Route::post('/masters/homes/upload-picture', [HomeController::class, 'uploadPicture']);
    Route::delete('/masters/homes/delete-picture/{home}', [HomeController::class, 'destroyPicture']);
    Route::post('/masters/homes/{home}', [HomeController::class, 'activatedData']);
    Route::resource('/masters/homes', HomeController::class);

    Route::get('/masters/categories/get-all-data', [CategoryController::class, 'getAllData']);
    Route::post('/masters/categories/{category}', [CategoryController::class, 'activatedData']);
    Route::resource('/masters/categories', CategoryController::class);

    Route::get('/masters/rooms/get-all-data', [RoomController::class, 'getAllData']);
    Route::get('/masters/rooms/upload-picture/{room}', [RoomController::class, 'getDataUpload']);
    Route::get('/masters/rooms/view-picture/{room}', [RoomController::class, 'showPicture']);
    Route::post('/masters/rooms/upload-picture', [RoomController::class, 'uploadPicture']);
    Route::delete('/masters/rooms/delete-picture/{room}', [RoomController::class, 'destroyPicture']);
    Route::resource('/masters/rooms', RoomController::class);

    Route::get('/masters/food-snacks/get-all-data', [FoodSnackController::class, 'getAllData']);
    Route::get('/masters/food-snacks/upload-picture/{foodSnack}', [FoodSnackController::class, 'getDataUpload']);
    Route::post('/masters/food-snacks/upload-picture', [FoodSnackController::class, 'uploadPicture']);
    Route::delete('/masters/food-snacks/delete-picture/{foodSnack}', [FoodSnackController::class, 'destroyPicture']);
    Route::resource('/masters/food-snacks', FoodSnackController::class);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/utils/dropdowns/get-homes', [DropdownController::class, 'getHome']);
    Route::get('/utils/dropdowns/get-categories', [DropdownController::class, 'getCategory']);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
