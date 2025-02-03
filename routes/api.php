<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\KategoriControllers;
use App\Http\Controllers\LokasiControllers;
use App\Http\Controllers\PengaduanControllers;
use App\Http\Controllers\TanggapanControllers;
use App\Http\Middleware\AdminMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware(['api'])->group(function() {
    Route::post('/register', [UserControllers::class, 'register']);
    // Rute-rute API di sini
});


Route::post('/login', [UserControllers::class, 'login']);
Route::get('/get/users', [UserControllers::class, 'index']);
Route::get('/get/lokasi', [LokasiControllers::class, 'index']);
Route::get('/get/kategori', [KategoriControllers::class, 'index']);

Route::middleware('auth:sanctum')->group(function() { 
    Route::get('/get/user/{id}', [UserControllers::class, 'getUserById']);
    Route::post('/update/user/{id}', [UserControllers::class, 'updateUserById']);
    Route::delete('/delete/user/{id}', [UserControllers::class, 'deleteUserById']);
    
    Route::post('/logout', [UserControllers::class, 'logout']);
    
    Route::post('/add/kategori', [KategoriControllers::class, 'addKategori']);
    Route::delete('/delete/kategori/{id}', [KategoriControllers::class, 'deleteKategoriById']);
    
    Route::post('/add/lokasi', [LokasiControllers::class, 'addLokasi']);
    Route::delete('/delete/lokasi/{id}', [LokasiControllers::class, 'deleteLokasiById']);
    //pengaduan route
    Route::post('/add/pengaduan', [PengaduanControllers::class, 'addPengaduan']);
    Route::post('/status/pengaduan/{id}', [PengaduanControllers::class, 'updateStatusPengaduan']);
    Route::get('/get/pengaduan', [PengaduanControllers::class, 'index']);
    Route::get('/get/pengaduan/{id}', [PengaduanControllers::class, 'getPengaduanById']);
    Route::get('/pengaduan/user', [PengaduanControllers::class, 'getPengaduanByUserId']);

    //pengaduan route
    Route::post('/add/{id}/tanggapan', [TanggapanControllers::class, 'addTanggapan']);
    Route::get('/get/{id}/tanggapan', [TanggapanControllers::class, 'getTanggapanByPengaduanId']);
    Route::get('/get/tanggapan', [TanggapanControllers::class, 'index']);

});