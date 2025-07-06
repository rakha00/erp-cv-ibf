<?php

use App\Http\Controllers\TransaksiProdukPDFController;
use App\Http\Controllers\GajiKaryawanPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/transaksi-produk/{transaksi}/surat-jalan', [TransaksiProdukPDFController::class, 'downloadSuratJalan'])
    ->name('transaksi-produk.surat-jalan');

Route::get('/transaksi-produk/{transaksi}/invoice', [TransaksiProdukPDFController::class, 'downloadInvoice'])
    ->name('transaksi-produk.invoice');


Route::get('/karyawan/{karyawan}/slip-gaji', GajiKaryawanPdfController::class)->name('karyawan.slip-gaji');