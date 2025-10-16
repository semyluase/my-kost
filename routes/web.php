<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodSnackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\Master\CategoryOrderController;
use App\Http\Controllers\Master\RuleController;
use App\Http\Controllers\Master\Service\CleaningController;
use App\Http\Controllers\Master\Service\LaundryController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TMP\UserIdentityController;
use App\Http\Controllers\TransactionFoodSnackController;
use App\Http\Controllers\TransactionRentController;
use App\Http\Controllers\TransactionServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Utils\DropdownController;
use App\Http\Controllers\Utils\MasterController as UtilsMasterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    Route::get('/masters', [MasterController::class, 'index']);

    Route::get('/masters/rules/get-all-data', [RuleController::class, 'getAllData']);
    Route::put('/masters/rules/re-order', [RuleController::class, 'reOrder']);
    Route::resource('/masters/rules', RuleController::class);

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

    Route::get('/masters/category-orders/get-all-data', [CategoryOrderController::class, 'getAllData']);
    Route::resource('/masters/category-orders', CategoryOrderController::class);

    Route::get('/masters/food-snacks/get-all-data', [FoodSnackController::class, 'getAllData']);
    Route::get('/masters/food-snacks/upload-picture/{foodSnack}', [FoodSnackController::class, 'getDataUpload']);
    Route::post('/masters/food-snacks/upload-picture', [FoodSnackController::class, 'uploadPicture']);
    Route::delete('/masters/food-snacks/delete-picture/{foodSnack}', [FoodSnackController::class, 'destroyPicture']);
    Route::resource('/masters/food-snacks', FoodSnackController::class);

    Route::get('/masters/cleaning-price/get-all-data', [CleaningController::class, 'getAllData']);
    Route::resource('/masters/cleaning-price', CleaningController::class);

    Route::get('/masters/laundry-price/get-all-data', [LaundryController::class, 'getAllData']);
    Route::resource('/masters/laundry-price', LaundryController::class);

    Route::get('/masters/hotspots/get-all-data', [HotspotController::class, 'getAllData']);
    Route::resource('/masters/hotspots', HotspotController::class);

    Route::get('/transactions/rent-rooms/get-all-data', [TransactionRentController::class, 'getAllData']);
    Route::get('/transactions/rent-rooms/approved', [TransactionRentController::class, 'approvedPayment']);
    Route::get('/transactions/rent-rooms/change-room', [TransactionRentController::class, 'changeRoom']);
    Route::get('/transactions/rent-rooms/checkout', [TransactionRentController::class, 'checkout']);
    Route::get('/transactions/rent-rooms/detail-rents/{room}', [TransactionRentController::class, 'detailPayment']);
    Route::get('/transactions/rent-rooms/search-member', [TransactionRentController::class, 'searchMember']);
    Route::get('/transactions/rent-rooms/generate-pdf', [TransactionRentController::class, 'generatePdf']);
    Route::get('/transactions/rent-rooms/detail-rooms/{room}', [TransactionRentController::class, 'detailRoomTransaction']);
    Route::get('/transactions/rent-rooms/calendar-views', [TransactionRentController::class, 'calendarView']);
    Route::post('/transactions/rent-rooms/detail-rents/{room}', [TransactionRentController::class, 'saveDetailPayment']);
    Route::post('/transactions/rent-rooms/upload-identity', [UserIdentityController::class, 'uploadIdentity']);
    Route::post('/transactions/rent-rooms/upload-foto-orang', [UserIdentityController::class, 'uploadFotoOrang']);
    Route::post('/transactions/rent-rooms/change-room', [TransactionRentController::class, 'storeChangeRoom']);
    Route::post('/transactions/rent-rooms/checkout', [TransactionRentController::class, 'storeCheckout']);
    Route::post('/transactions/rent-rooms/canceled', [TransactionRentController::class, 'storeCanceled']);
    Route::resource('/transactions/rent-rooms', TransactionRentController::class);

    Route::get('/members/details/{member}', [MemberController::class, 'detail']);
    Route::get('/members/get-all-data', [MemberController::class, 'getAllData']);
    Route::resource('/members', MemberController::class);

    // Route::get('/inventories/receipts/create', [ReceiptController::class, 'create']);
    Route::get('/inventories/receipts/get-detail-data', [ReceiptController::class, 'getDetailData']);
    Route::get('/inventories/receipts/generateReport', [ReceiptController::class, 'generateReport']);
    Route::post('/inventories/receipts/posting', [ReceiptController::class, 'posting']);
    Route::delete('/inventories/receipts/details/{detail}', [ReceiptController::class, 'deleteDetail']);
    Route::resource('/inventories/receipts', ReceiptController::class);

    # laundry
    Route::get('/transactions/orders/laundry', [TransactionServiceController::class, 'indexLaundry']);
    Route::post('/transactions/orders/laundry/receive-laundry', [TransactionServiceController::class, 'receiveLaundry']);
    Route::post('/transactions/orders/laundry/finish-laundry', [TransactionServiceController::class, 'finishLaundry']);
    Route::post('/transactions/orders/laundry/take-laundry', [TransactionServiceController::class, 'takeLaundry']);
    Route::get('/transactions/orders/laundry/create', [TransactionServiceController::class, 'createLaundry']);
    Route::get('/transactions/orders/laundry/get-detail', [TransactionServiceController::class, 'getDetailLaundry']);
    Route::get('/transactions/orders/laundry/get-all-data', [TransactionServiceController::class, 'getAllDataLaundry']);
    Route::post('/transactions/orders/laundry', [TransactionServiceController::class, 'storeLaundry']);
    Route::post('/transactions/orders/laundry/store-payment', [TransactionServiceController::class, 'storeLaundryPayment']);

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

    Route::get('/transactions/generate-receipt', [TransactionServiceController::class, 'generatePdf']);
    Route::get('/transactions/generate-email-receipt', [TransactionServiceController::class, 'generatePdfEmail']);
    Route::resource('/transactions/orders', TransactionServiceController::class);

    Route::get('/settings/roles/get-all-data', [RoleController::class, 'getAllData']);
    Route::resource('/settings/roles', RoleController::class);

    Route::get('/settings/role-menus/get-menu-data', [RoleMenuController::class, 'getMenuData']);
    Route::resource('/settings/role-menus', RoleMenuController::class);

    Route::get('/reports/generate-data', [ReportController::class, 'downloadExcel']);
    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/settings/users/get-all-data', [UserController::class, 'getAllData']);
    Route::get('/settings/users/profiles', [UserController::class, 'profile']);
    Route::resource('/settings/users', UserController::class);

    Route::get('/utils/dropdowns/get-homes', [DropdownController::class, 'getHome']);
    Route::get('/utils/dropdowns/get-categories', [DropdownController::class, 'getCategory']);
    Route::get('/utils/dropdowns/get-categories-transaction', [DropdownController::class, 'getCategoryForTransaction']);
    Route::get('/utils/dropdowns/get-roles', [DropdownController::class, 'getRole']);
    Route::get('/utils/dropdowns/get-room', [DropdownController::class, 'getRoom']);
    Route::get('/utils/dropdowns/get-room-by-category', [DropdownController::class, 'getRoomByCategory']);
    Route::get('/utils/dropdowns/get-all-room-by-category', [DropdownController::class, 'getAllRoomByCategory']);
    Route::get('/utils/dropdowns/get-member', [DropdownController::class, 'getMember']);
    Route::get('/utils/dropdowns/get-bank', [DropdownController::class, 'getBank']);
    Route::get('/utils/dropdowns/get-category-laundry', [DropdownController::class, 'getCategoryLaundry']);
    Route::get('/utils/dropdowns/get-items', [DropdownController::class, 'getItems']);
    Route::get('/utils/dropdowns/get-category-orders', [DropdownController::class, 'getCategoryOrder']);

    Route::get('/utils/prices/get-laundry', [DropdownController::class, 'getLaundry']);

    Route::get('/utils/master/user', [UtilsMasterController::class, 'getUserMember']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
