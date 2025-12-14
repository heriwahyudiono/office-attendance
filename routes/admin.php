<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\BlockedDeviceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OfficeLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Management
    Route::resource('employees', EmployeeController::class)->except(['show']);

    // Attendance Management
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{id}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::delete('/attendances/{id}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    // Secure photo access
    Route::get('/attendance-photo/{id}', [AttendanceController::class, 'photo'])->name('attendances.photo');

    // Office Locations
    Route::get('/office-locations', [OfficeLocationController::class, 'index'])->name('office-locations.index');
    Route::post('/office-locations', [OfficeLocationController::class, 'store'])->name('office-locations.store');
    // Add other resource routes if needed later (edit/update/delete)

    // Blocked Devices
    Route::get('/blocked-devices', [BlockedDeviceController::class, 'index'])->name('blocked-devices.index');
    Route::post('/blocked-devices', [BlockedDeviceController::class, 'store'])->name('blocked-devices.store');
    Route::delete('/blocked-devices/{id}', [BlockedDeviceController::class, 'destroy'])->name('blocked-devices.destroy');
});
