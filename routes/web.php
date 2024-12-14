<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodSnackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TransactionFoodSnackController;
use App\Http\Controllers\TransactionRentController;
use App\Http\Controllers\TransactionServiceController;
use App\Http\Controllers\UserController;
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
    Route::get('/masters/categories/upload-picture/{category}', [CategoryController::class, 'getDataUpload']);
    Route::get('/masters/categories/view-picture/{category}', [CategoryController::class, 'showPicture']);
    Route::post('/masters/categories/upload-picture', [CategoryController::class, 'uploadPicture']);
    Route::post('/masters/categories/{category}', [CategoryController::class, 'activatedData']);
    Route::delete('/masters/categories/delete-picture/{category}', [CategoryController::class, 'destroyPicture']);
    Route::resource('/masters/categories', CategoryController::class);

    Route::get('/masters/rooms/get-all-data', [RoomController::class, 'getAllData']);
    Route::get('/masters/rooms/view-picture/{room}', [RoomController::class, 'showPicture']);
    Route::resource('/masters/rooms', RoomController::class);

    Route::get('/masters/food-snacks/get-all-data', [FoodSnackController::class, 'getAllData']);
    Route::get('/masters/food-snacks/upload-picture/{foodSnack}', [FoodSnackController::class, 'getDataUpload']);
    Route::post('/masters/food-snacks/upload-picture', [FoodSnackController::class, 'uploadPicture']);
    Route::delete('/masters/food-snacks/delete-picture/{foodSnack}', [FoodSnackController::class, 'destroyPicture']);
    Route::resource('/masters/food-snacks', FoodSnackController::class);

    Route::resource('/transactions/rent-rooms', TransactionRentController::class);

    // Route::get('/inventories/receipts/create', [ReceiptController::class, 'create']);
    Route::get('/inventories/receipts/get-detail-data', [ReceiptController::class, 'getDetailData']);
    Route::post('/inventories/receipts/posting', [ReceiptController::class, 'posting']);
    Route::delete('/inventories/receipts/details/{detail}', [ReceiptController::class, 'deleteDetail']);
    Route::resource('/inventories/receipts', ReceiptController::class);

    # laundry
    Route::get('/transactions/orders/laundry', [TransactionServiceController::class, 'indexLaundry']);
    Route::get('/transactions/orders/laundry/create', [TransactionServiceController::class, 'createLaundry']);
    Route::get('/transactions/orders/laundry/get-detail', [TransactionServiceController::class, 'getDetailLaundry']);
    Route::get('/transactions/orders/laundry/get-all-data', [TransactionServiceController::class, 'getAllDataLaundry']);
    Route::post('/transactions/orders/laundry', [TransactionServiceController::class, 'storeLaundry']);

    # cleaning
    Route::get('/transactions/orders/cleaning', [TransactionServiceController::class, 'indexCleaning']);
    Route::get('/transactions/orders/cleaning/create', [TransactionServiceController::class, 'createCleaning']);
    Route::get('/transactions/orders/cleaning/get-detail', [TransactionServiceController::class, 'getDetailCleaning']);
    Route::get('/transactions/orders/cleaning/get-all-data', [TransactionServiceController::class, 'getAllDataCleaning']);
    Route::post('/transactions/orders/cleaning', [TransactionServiceController::class, 'storeCleaning']);
    Route::post('/transactions/orders/cleaning/start', [TransactionServiceController::class, 'startCleaning']);
    Route::post('/transactions/orders/cleaning/stop', [TransactionServiceController::class, 'stopCleaning']);

    # top up
    Route::get('/transactions/orders/top-up', [TransactionServiceController::class, 'indexTopUp']);
    Route::get('/transactions/orders/top-up/detail', [TransactionServiceController::class, 'detailTopUp']);
    Route::post('/transactions/orders/top-up', [TransactionServiceController::class, 'storeTopUp']);

    # food snack
    Route::get('/transactions/orders/food-snack', [TransactionFoodSnackController::class, 'index']);
    Route::get('/transactions/orders/food-snack/get-all-data', [TransactionFoodSnackController::class, 'getAllData']);
    Route::get('/transactions/orders/food-snack/get-list-menu', [TransactionFoodSnackController::class, 'getListMenu']);
    Route::get('/transactions/orders/food-snack/payments/{header}', [TransactionFoodSnackController::class, 'getPayment']);
    Route::get('/transactions/orders/food-snack/receipt', [TransactionFoodSnackController::class, 'receipt']);
    Route::get('/transactions/orders/food-snack/{id}/edit', [TransactionFoodSnackController::class, 'edit']);
    Route::get('/transactions/orders/food-snack/create', [TransactionFoodSnackController::class, 'create']);
    Route::post('/transactions/orders/food-snack', [TransactionFoodSnackController::class, 'store']);
    Route::post('/transactions/orders/food-snack/payments', [TransactionFoodSnackController::class, 'storePayment']);
    Route::put('/transactions/orders/food-snack', [TransactionFoodSnackController::class, 'update']);
    Route::delete('/transactions/orders/food-snack/{detail}', [TransactionFoodSnackController::class, 'destroyDetail']);

    Route::resource('/transactions/orders', TransactionServiceController::class);

    Route::get('/settings/roles/get-all-data', [RoleController::class, 'getAllData']);
    Route::resource('/settings/roles', RoleController::class);

    Route::get('/settings/role-menus/get-menu-data', [RoleMenuController::class, 'getMenuData']);
    Route::resource('/settings/role-menus', RoleMenuController::class);

    Route::get('/settings/users/get-all-data', [UserController::class, 'getAllData']);
    Route::get('/settings/users/profiles', [UserController::class, 'profile']);
    Route::resource('/settings/users', UserController::class);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/utils/dropdowns/get-homes', [DropdownController::class, 'getHome']);
    Route::get('/utils/dropdowns/get-categories', [DropdownController::class, 'getCategory']);
    Route::get('/utils/dropdowns/get-roles', [DropdownController::class, 'getRole']);
    Route::get('/utils/dropdowns/get-room', [DropdownController::class, 'getRoom']);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
