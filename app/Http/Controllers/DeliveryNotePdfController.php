<?php

namespace App\Http\Controllers;

use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNotePdfController extends Controller
{
	public function __invoke(TransaksiProduk $transaksi)
	{
		$filename = 'surat-jalan-' . str_replace(['/', '\\'], '-', $transaksi->no_surat_jalan) . '.pdf';
		$pdf = Pdf::loadView('pdf.surat-jalan', ['transaksi' => $transaksi])->setPaper('a4', 'portrait');
		return $pdf->download($filename);
	}
}