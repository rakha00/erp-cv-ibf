<?php

use App\Http\Controllers\DeliveryNotePdfController;
use App\Http\Controllers\GajiKaryawanPdfController;
use App\Http\Controllers\InvoicePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/transaksi-produk/{transaksi}/surat-jalan', DeliveryNotePdfController::class)
    ->name('transaksi-produk.surat-jalan');

Route::get('/transaksi-produk/{transaksi}/invoice', InvoicePdfController::class)
    ->name('transaksi-produk.invoice');

Route::get('/karyawan/{karyawan}/slip-gaji', GajiKaryawanPdfController::class)->name('karyawan.slip-gaji');
