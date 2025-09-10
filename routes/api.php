<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerSparepartController;
use App\Http\Controllers\DailyNetRevenueController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GeraiController;
use App\Http\Controllers\HistorySparepartController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\MotorPartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SealController;
use App\Http\Controllers\ServiceCustomerController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseSealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/orders/create', [OrderController::class, 'store']);
Route::get('/customers/{customer}', [CustomerController::class, 'show']);
Route::post('/customers/{id}/claim-warranty', [CustomerController::class, 'claimWarranty']);
Route::get('/customers', [CustomerController::class, 'getByDateRange']);

Route::prefix('antrian')->group(function () {
    Route::get('/', [AntrianController::class, 'getAntrianByGeraiId']);
    Route::get("/all", [AntrianController::class, "getAntrianAllOutlet"]);
    Route::get("/getAll", [AntrianController::class, "getAntrianSemuaGerai"]);
});

Route::prefix('gerais')->group(function () {
    Route::get('/getAll', [GeraiController::class, 'getAllGerais']);
    Route::get('/{id}', [GeraiController::class, 'getGeraiById']);
});

Route::prefix('motorParts')->group(function () {
    Route::get('/', [MotorPartController::class, 'getAllMotorParts']);
    Route::get('/{id}', [MotorPartController::class, 'getMotorPartById']);
});

Route::prefix('motors')->group(function () {
    Route::get('/getAll', [MotorController::class, 'getAllMotors']);
    Route::get('/{id}', [MotorController::class, 'getMotorById']);
});

Route::prefix('categories')->group(function () {
    Route::get('/getAll', [CategoryController::class, 'getAllCategories']);
    Route::get('/{id}', [CategoryController::class, 'getCategoryById']);
});

Route::prefix('subcategories')->group(function () {
    Route::get('/getAll', [SubcategoryController::class, 'getAllSubcategories']);
    Route::get('/{id}', [SubcategoryController::class, 'getSubcategoryById']);
});

Route::prefix('seals')->group(function () {
    Route::get('/getAll', [SealController::class, 'getAllSeals']);
    Route::get('/{id}', [SealController::class, 'getSeal']);
    Route::get('/gerai/{geraiId}', [SealController::class, 'getSealsByGerai']);
    Route::get('/stock-requests/gerai/{geraiId}', [StockRequestController::class, 'getStockRequestsByGerai']);
    Route::get('/stock-requests/getAll', [StockRequestController::class, 'getAllStockRequests']);
});

Route::prefix('expenses')->group(function () {
    Route::post('/create', [ExpenseController::class, 'createExpense']);
    Route::get('/all-gerais', [ExpenseController::class, 'getAllExpensesAll']);
    Route::get('/get-by-date-range', [ExpenseController::class, 'getExpensesByDateRange']);
    Route::get('/all', [ExpenseController::class, 'getAllExpenses']);
});

Route::prefix('daily-net-revenue')->group(function () {
    Route::get('/revenue-by-period', [DailyNetRevenueController::class, 'getRevenueByPeriod']);
    Route::post('/', [DailyNetRevenueController::class, 'createDailyNetRevenue']);
    Route::post('/calculate', [DailyNetRevenueController::class, 'calculateDailyNetRevenue']);
    Route::get('/daily', [DailyNetRevenueController::class, 'getOrCalculateDailyNetRevenue']);
    Route::get('/total-revenue', [DailyNetRevenueController::class, 'getTotalRevenue']);
    Route::get('/total-gross-revenue', [DailyNetRevenueController::class, 'getTotalGrossRevenue']);
    Route::get('/daily-trend-all', [DailyNetRevenueController::class, 'dailyTrendAll']);
    Route::get('/daily-trend', [DailyNetRevenueController::class, 'getDailyTrend']);
    Route::get('/daily-income-expense', [DailyNetRevenueController::class, 'getIncomeExpenseDaily']);
    Route::get('/periodic-trend', [DailyNetRevenueController::class, 'getPeriodicTrend']);
    Route::get('/', [DailyNetRevenueController::class, 'getAllDailyNetRevenues']);
    Route::put('/{id}', [DailyNetRevenueController::class, 'updateDailyNetRevenue']);
    Route::delete('/{id}', [DailyNetRevenueController::class, 'deleteDailyNetRevenue']);
});

Route::resource('service-customer', ServiceCustomerController::class);
Route::resource('customer-profile', CustomerProfileController::class);
Route::resource('customer', CustomerController::class);
Route::resource('image', ImageController::class);
Route::resource("customer-sparepart", CustomerSparepartController::class);

// Authenticated Routes (JWT with auth:api)
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Antrian Routes
    Route::prefix('antrian')->group(function () {
        Route::put('/{id}', [AntrianController::class, 'updateAntrian'])->middleware('role:ADMIN');
        Route::post('/finish/{id}', [AntrianController::class, 'finishOrder']);
        Route::post('/cancel/{id}', [AntrianController::class, 'cancelOrder']);
    });

    Route::prefix('gerais')->group(function () {
        Route::post('/create', [GeraiController::class, 'createGerai']);
        Route::put('/update/{id}', [GeraiController::class, 'updateGerai']);
        Route::delete('/delete/{id}', [GeraiController::class, 'deleteGerai']);
    });

    Route::prefix('motors')->group(function () {
        Route::post('/', [MotorController::class, 'createMotor']);
        Route::put('/{id}', [MotorController::class, 'updateMotor']);
        Route::delete('/{id}', [MotorController::class, 'deleteMotor']);
    });

    Route::prefix('motorParts')->group(function () {
        Route::post('/', [MotorPartController::class, 'createMotorPart']);
        Route::put('/{id}', [MotorPartController::class, 'updateMotorPart']);
        Route::delete('/{id}', [MotorPartController::class, 'deleteMotorPart']);
    });

    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'createCategory']);
        Route::put('/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);
    });

    Route::prefix('subcategories')->group(function () {
        Route::post('/', [SubcategoryController::class, 'createSubcategory']);
        Route::put('/{id}', [SubcategoryController::class, 'updateSubcategory']);
        Route::delete('/{id}', [SubcategoryController::class, 'deleteSubcategory']);
    });

    Route::prefix('seals')->middleware(['auth:api', 'role:ADMIN,GUDANG,CEO,FINANCE'])->group(function () {
        Route::put('/update/{seal}', [SealController::class, 'updateSeal']);
        Route::delete('/delete/{id}', [SealController::class, 'deleteSeal']);
        Route::post('/stock-requests', [StockRequestController::class, 'requestSeal']);
        Route::put('/stock-requests/{stockRequest}', [StockRequestController::class, 'updateStockRequest']);
        Route::post('/stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approveStockRequest']);
        Route::post('/stock-requests/{stockRequest}/rejected', [StockRequestController::class, 'rejectStockRequest']);
    });

    Route::prefix('warehouse-seals')->middleware(['auth:api', 'role:ADMIN,GUDANG'])->group(function () {
        Route::post('/create', [WarehouseSealController::class, 'store']);
        Route::get('/', [WarehouseSealController::class, 'index']);
        Route::get('/{id}', [WarehouseSealController::class, 'show']);
        Route::put('/{id}', [WarehouseSealController::class, 'update']);
        Route::delete('/{id}', [WarehouseSealController::class, 'destroy']);
    });

    Route::resource('user', UserController::class);
});



