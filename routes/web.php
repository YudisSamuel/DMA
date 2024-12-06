<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DmaPredictionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataProductController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

// Route Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.post');

// Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Register
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');


// route untuk halaman home
Route::get('/Home', function () {
    return view('Home');
})->middleware('auth');
Route::get('/Home', [HomeController::class, 'index'])->name('Home');


// Route DMA Prediction
Route::get('/dma_prediction', [DmaPredictionController::class, 'index'])->name('dma_prediction')->middleware('auth');
Route::post('/dma_prediction', [DmaPredictionController::class, 'dma_prediction']);
// Route untuk ambil data penjualan (API)
Route::post('/get-sales-data', [DmaPredictionController::class, 'getSalesData']);
Route::post('/dma-prediction', [DmaPredictionController::class, 'getMonthlySales']);


// Route Data Product
Route::get('/DataProduct', [DataProductController::class, 'index'])->name('DataProduct');
Route::post('/DataProduct', [DataProductController::class, 'store'])->name('dataproduct.store');

// Route Data Product view
Route::get('/DataProduct/{id}', [DataProductController::class, 'show'])->name('dataproduct.show');

// Rute untuk menampilkan form edit
Route::get('/DataProduct/{id}/edit', [DataProductController::class, 'edit'])->name('dataproduct.edit');

// Rute untuk memperbarui data produk
Route::put('/DataProduct/{id}', [DataProductController::class, 'update'])->name('dataproduct.update');

// Rute untuk menghapus data produk
Route::delete('/DataProduct/{id}', [DataProductController::class, 'destroy'])->name('dataproduct.destroy');


// route laporan
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');


// Profile
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Route untuk mendapatkan total penjualan berdasarkan tahun
Route::get('/total-penjualan/{year}', [HomeController::class, 'getTotalPenjualan']);

Route::get('/api/sales-data', [DmaPredictionController::class, 'getMonthlySales']);

// save peramalan
Route::post('/save-forecast', [DmaPredictionController::class, 'saveForecastToDatabase'])->name('save.forecast');

// cetak pdf
Route::get('/laporan/cetak/{id}', [LaporanController::class, 'cetak'])->name('laporan.cetak');


// menampilkan user
Route::resource('user', UserController::class);
Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/user/create', [UserController::class, 'showRegistrationForm'])->name('user.create');
Route::post('/user', [UserController::class, 'register'])->name('user.store');
Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

Route::post('/predict', [DmaPredictionController::class, 'predict'])->name('predict');

Route::post('/laporan/clear', [LaporanController::class, 'clear'])->name('laporan.clear');
Route::post('/laporan/restore', [LaporanController::class, 'restore'])->name('laporan.restore');

// Upload CSV
Route::post('/dataproduct/upload', [DataProductController::class, 'uploadCsv'])->name('dataproduct.upload');
