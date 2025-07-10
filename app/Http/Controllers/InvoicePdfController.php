<?php

namespace App\Http\Controllers;

use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
	public function __invoke(TransaksiProduk $transaksi)
	{
		$filename = 'invoice-' . str_replace(['/', '\\'], '-', $transaksi->no_invoice) . '.pdf';
		$pdf = Pdf::loadView('pdf.invoice', ['transaksi' => $transaksi])->setPaper('a4', 'portrait');
		return $pdf->download($filename);
	}
}