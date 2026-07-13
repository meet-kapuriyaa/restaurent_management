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

    // Customer and order lookup routes
    Route::get('/customers/lookup', [OrderController::class, 'lookupCustomer'])->name('customers.lookup');
    Route::get('/customers/search', [OrderController::class, 'searchCustomers'])->name('customers.search');
    Route::get('/orders/active-by-table/{table}', [OrderController::class, 'getActiveOrderByTable'])->name('orders.active-by-table');

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
        Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
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
        
        // Category CRUD
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::post('/categories/sort', [AdminController::class, 'sortCategories'])->name('admin.categories.sort');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
        
        // Table CRUD
        Route::post('/tables', [AdminController::class, 'storeTable'])->name('admin.tables.store');
        Route::put('/tables/{table}', [AdminController::class, 'updateTable'])->name('admin.tables.update');
        Route::delete('/tables/{table}', [AdminController::class, 'deleteTable'])->name('admin.tables.delete');
        Route::patch('/tables/{table}/status', [AdminController::class, 'toggleTableStatus'])->name('admin.tables.status');
        
        // Order Controls
        Route::get('/orders/{order}/invoice', [AdminController::class, 'invoice'])->name('admin.orders.invoice');
        Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
        Route::patch('/orders/{order}/payment', [AdminController::class, 'updateOrderPayment'])->name('admin.orders.payment');
        Route::delete('/orders/{order}', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');

        // CRM Controls
        Route::get('/crm', [AdminController::class, 'crmIndex'])->name('admin.crm.index');
        Route::post('/crm', [AdminController::class, 'crmStore'])->name('admin.crm.store');
    });

    // Dedicated Role Management Page & Actions (Requires 'manage_roles' permission)
    Route::middleware('permission:manage_roles')->group(function () {
        Route::get('/admin-role', [AdminController::class, 'adminRole'])->name('admin.roles.index');
        Route::post('/admin/permissions', [AdminController::class, 'updatePermissions'])->name('admin.permissions.update');
        Route::post('/admin/roles', [AdminController::class, 'createRole'])->name('admin.roles.create');
        Route::post('/admin/roles/toggle-status', [AdminController::class, 'toggleRoleStatus'])->name('admin.roles.toggle-status');
        Route::post('/admin/features/toggle', [AdminController::class, 'toggleFeature'])->name('admin.features.toggle');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::patch('/admin/users/{user}/status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.status');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
        Route::patch('/admin/users/{user}/salary', [AdminController::class, 'updateUserSalary'])->name('admin.users.salary');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::delete('/admin/roles/{role}', [AdminController::class, 'deleteRole'])->name('admin.roles.delete');

        // Dynamic Modules Menu Builder Routes
        Route::get('/admin/modules', [\App\Http\Controllers\ModuleController::class, 'index'])->name('admin.modules.index');
        Route::post('/admin/modules', [\App\Http\Controllers\ModuleController::class, 'store'])->name('admin.modules.store');
        Route::put('/admin/modules/{module}', [\App\Http\Controllers\ModuleController::class, 'update'])->name('admin.modules.update');
        Route::delete('/admin/modules/{module}', [\App\Http\Controllers\ModuleController::class, 'destroy'])->name('admin.modules.destroy');
        Route::post('/admin/modules/{module}/toggle', [\App\Http\Controllers\ModuleController::class, 'toggle'])->name('admin.modules.toggle');
        Route::post('/admin/modules/sort', [\App\Http\Controllers\ModuleController::class, 'updateOrder'])->name('admin.modules.sort');

        // Dynamic Icons CRUD Routes
        Route::get('/icon', [\App\Http\Controllers\IconController::class, 'index'])->name('admin.icons.index');
        Route::post('/icon', [\App\Http\Controllers\IconController::class, 'store'])->name('admin.icons.store');
        Route::put('/icon/{icon}', [\App\Http\Controllers\IconController::class, 'update'])->name('admin.icons.update');
        Route::delete('/icon/{icon}', [\App\Http\Controllers\IconController::class, 'destroy'])->name('admin.icons.destroy');
    });
});
