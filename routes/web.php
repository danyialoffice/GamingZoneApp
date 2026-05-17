<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;
use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\PCController;
use App\Http\Controllers\Tenant\PlayerBookingController;
use App\Http\Controllers\Tenant\RoomController;
use App\Http\Controllers\Website\BookingStatusController;
use App\Http\Controllers\Website\PlayerDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/select-zone', [HomeController::class, 'selectZone'])->name('select-zone');
Route::get('/zone/{slug}', [HomeController::class, 'website'])->name('website.index');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
    Route::get('/select-tenant', [AuthController::class, 'selectTenant'])->name('select-tenant')->middleware('auth');
    Route::post('/set-tenant', [AuthController::class, 'setTenant'])->name('set-tenant')->middleware('auth');
    Route::get('/join-tenant/{tenant}', [AuthController::class, 'joinTenant'])->name('join-tenant')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/
Route::prefix('notifications')->middleware('auth')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('recent');
    Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/delete-all-read', [App\Http\Controllers\NotificationController::class, 'destroyAllRead'])->name('destroy-all-read');
});

/*
|--------------------------------------------------------------------------
| Player Dashboard Route (Public Website)
|--------------------------------------------------------------------------
*/
Route::get('/player/dashboard', [PlayerDashboardController::class, 'index'])->name('player.dashboard')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Player Booking Routes (Public Website)
|--------------------------------------------------------------------------
*/
Route::prefix('booking')->middleware('auth')->group(function () {
    // PC Status Dashboard
    Route::get('/pc-status', [BookingStatusController::class, 'index'])->name('website.booking.pc-status');
    Route::post('/set-view-mode', [BookingStatusController::class, 'setViewMode'])->name('website.booking.set-view-mode');
    Route::get('/available-pcs-list', [BookingStatusController::class, 'getAvailablePcs'])->name('website.booking.available-pcs-list');
    
    Route::get('/create', [PlayerBookingController::class, 'create'])->name('website.booking.create');
    Route::post('/', [PlayerBookingController::class, 'store'])->name('website.booking.store');
    Route::get('/my-bookings', [PlayerBookingController::class, 'myBookings'])->name('website.booking.my-bookings');
    Route::get('/confirmation/{booking}', [PlayerBookingController::class, 'confirmation'])->name('website.booking.confirmation');
    Route::get('/group/{bookingGroupId}', [PlayerBookingController::class, 'groupBooking'])->name('website.booking.group');
    
    // API for getting available PCs (MUST be before {booking} route)
    Route::get('/available-pcs', [PlayerBookingController::class, 'getAvailablePcs'])->name('website.booking.available-pcs');
    
    Route::get('/{booking}', [BookingController::class, 'show'])->name('website.booking.show');
    Route::post('/{booking}/cancel', [PlayerBookingController::class, 'cancel'])->name('website.booking.cancel');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('super-admin')->middleware(['auth', 'role:super_admin'])->name('super-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
    // Tenant Management
    Route::get('/tenants', [SuperAdminTenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [SuperAdminTenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [SuperAdminTenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}', [SuperAdminTenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenant}/edit', [SuperAdminTenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [SuperAdminTenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [SuperAdminTenantController::class, 'destroy'])->name('tenants.destroy');
});

/*
|--------------------------------------------------------------------------
| Tenant Dashboard Routes (Admin & Staff)
|--------------------------------------------------------------------------
*/
Route::prefix('tenant')->middleware(['auth', 'tenant'])->name('tenant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    
    // PCs
    Route::get('/pcs', [PCController::class, 'index'])->name('pcs.index');
    Route::get('/pcs/create', [PCController::class, 'create'])->name('pcs.create');
    Route::post('/pcs', [PCController::class, 'store'])->name('pcs.store');
    Route::get('/pcs/{pc}', [PCController::class, 'show'])->name('pcs.show');
    Route::get('/pcs/{pc}/edit', [PCController::class, 'edit'])->name('pcs.edit');
    Route::put('/pcs/{pc}', [PCController::class, 'update'])->name('pcs.update');
    Route::delete('/pcs/{pc}', [PCController::class, 'destroy'])->name('pcs.destroy');
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/pending', [BookingController::class, 'pending'])->name('bookings.pending');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    
    // Tenant Settings (for email notifications)
    Route::get('/settings/notifications', [App\Http\Controllers\Tenant\SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('/settings/notifications', [App\Http\Controllers\Tenant\SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
});
