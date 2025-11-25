<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\IncomingTransactionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('barangs', BarangController::class);
    Route::get('/incoming-transactions', [IncomingTransactionController::class, 'index'])->name('incoming_transactions.index');
    Route::get('/incoming-transactions/create', [IncomingTransactionController::class, 'create'])->name('incoming_transactions.create');
    Route::post('/incoming-transactions', [IncomingTransactionController::class, 'store'])->name('incoming_transactions.store');
    Route::get('/incoming-transactions/{incoming_transaction}', [IncomingTransactionController::class, 'show'])->name('incoming_transactions.show');
    Route::resource('transaksis', TransaksiController::class);
    Route::get('/transaksis/{penjualan}/print', [TransaksiController::class, 'print'])->name('transaksis.print');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/inventaris', [LaporanController::class, 'inventaris'])->name('laporan.inventaris');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/stock-history', [StockHistoryController::class, 'index'])->name('stock_history.index');
});



require __DIR__.'/auth.php';
