<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GajiKaryawanPdfController extends Controller
{
    public function __invoke(Karyawan $karyawan, Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = (int) $request->input('bulan', date('n'));

        $karyawan->load(['penghasilanKaryawanDetails' => function ($query) use ($tahun, $bulan) {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        }]);

        $lembur = $karyawan->penghasilanKaryawanDetails->sum('lembur');
        $bonus = $karyawan->penghasilanKaryawanDetails->sum('bonus');
        $kasbon = $karyawan->penghasilanKaryawanDetails->sum('kasbon');
        $totalGaji = $karyawan->gaji_pokok + $lembur + $bonus;
        $gajiDiterima = $totalGaji - $kasbon;

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'karyawan' => $karyawan,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'lembur' => $lembur,
            'bonus' => $bonus,
            'kasbon' => $kasbon,
            'totalGaji' => $totalGaji,
            'gajiDiterima' => $gajiDiterima,
        ]);

        return $pdf->download('slip-gaji-' . $karyawan->nama . '-' . $bulan . '-' . $tahun . '.pdf');
    }
}