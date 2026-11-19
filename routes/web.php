<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

// Public blog: guests can only see published articles.
Route::get('/', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.list');
Route::get('/blog/{article}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('articles', ArticleController::class);

    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserRoleController::class, 'update'])->name('users.role.update');

    // Role definitions and permission assignment are deliberately separate pages.
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::patch('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::put('/permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
