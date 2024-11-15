<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Auth;

// หน้าแรก (แสดงห้องทั้งหมด)
Route::get('/', [RoomController::class, 'index'])->name('rooms.index');

// หน้าเพิ่มห้อง
Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

// การจองห้อง
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

// การอนุมัติและปฏิเสธการจอง (เฉพาะ admin)
Route::patch('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

// Auth Routes (สำหรับ login/logout)
Auth::routes();

// หน้า Home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
