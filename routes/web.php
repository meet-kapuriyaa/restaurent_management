<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Guest Routes (Login / Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Central Home Portal Landing Page
    Route::get('/', [AuthController::class, 'home'])->name('home');

    // Profile Management Routes
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile/info', [AuthController::class, 'updateProfileInfo'])->name('profile.info.update');
    Route::post('/profile/password', [AuthController::class, 'updateProfilePassword'])->name('profile.password.update');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Waiter Terminal Routes (Requires 'waiter_terminal' permission)
    Route::middleware('permission:waiter_terminal')->group(function () {
        Route::get('/order', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/pending', [OrderController::class, 'getPendingOrders'])->name('orders.pending');
        Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    });

    // Kitchen Terminal Routes (Requires 'kitchen_terminal' permission)
    Route::middleware('permission:kitchen_terminal')->group(function () {
        Route::get('/kitchen', [OrderController::class, 'kitchen'])->name('orders.kitchen');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });

    // Admin Control Panel Routes (Requires 'admin_panel' permission)
    Route::prefix('admin')->middleware('permission:admin_panel')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        
        // Food Item CRUD
        Route::post('/food-items', [AdminController::class, 'storeFoodItem'])->name('admin.food-items.store');
        Route::put('/food-items/{foodItem}', [AdminController::class, 'updateFoodItem'])->name('admin.food-items.update');
        Route::delete('/food-items/{foodItem}', [AdminController::class, 'deleteFoodItem'])->name('admin.food-items.delete');
        Route::patch('/food-items/{foodItem}/status', [AdminController::class, 'toggleFoodItemStatus'])->name('admin.food-items.status');
        
        // Order Controls
        Route::get('/orders/{order}/invoice', [AdminController::class, 'invoice'])->name('admin.orders.invoice');
        Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
        Route::patch('/orders/{order}/payment', [AdminController::class, 'updateOrderPayment'])->name('admin.orders.payment');
        Route::delete('/orders/{order}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');

        // Access Control & User Role Management
        Route::post('/permissions', [AdminController::class, 'updatePermissions'])->name('admin.permissions.update');
        Route::post('/roles', [AdminController::class, 'createRole'])->name('admin.roles.create');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });
});
