<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SensorSimulatorController;
use App\Http\Controllers\Admin\SmsController;
use App\Http\Controllers\Admin\TrainController;
use App\Http\Controllers\Admin\TripController as AdminTripController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'trips.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::post('/trips/{trip}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('trains', TrainController::class)->except('show');

    Route::resource('trips', AdminTripController::class)->except('show');
    Route::get('trips/{trip}', [AdminTripController::class, 'show'])->name('trips.show');
    Route::post('trips/{trip}/delay', [AdminTripController::class, 'delay'])->name('trips.delay');

    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');

    Route::get('simulator', [SensorSimulatorController::class, 'index'])->name('simulator.index');
    Route::post('simulator', [SensorSimulatorController::class, 'send'])->name('simulator.send');
});
