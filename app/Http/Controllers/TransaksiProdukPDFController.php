<?php

namespace App\Http\Controllers;

use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransaksiProdukPDFController extends Controller
{
    public function downloadInvoice(TransaksiProduk $transaksi)
    {
        $filename = 'invoice-' . str_replace(['/', '\\'], '-', $transaksi->no_invoice) . '.pdf';
        $pdf = Pdf::loadView('pdf.invoice', ['transaksi' => $transaksi])->setPaper('a4', 'portrait')->setOption('margin-left', 0)->setOption('margin-right', 0)->setOption('margin-top', 0)->setOption('margin-bottom', 0);
        return $pdf->download($filename);
    }

    public function downloadSuratJalan(TransaksiProduk $transaksi)
    {
        $filename = 'surat-jalan-' . str_replace(['/', '\\'], '-', $transaksi->no_surat_jalan) . '.pdf';
        $pdf = Pdf::loadView('pdf.surat-jalan', ['transaksi' => $transaksi])->setPaper('a4', 'portrait');
        return $pdf->download($filename);
    }
}
