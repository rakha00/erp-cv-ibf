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

        $karyawan->load([
            'penghasilanKaryawanDetails' => function ($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            },
        ]);

        $penghasilanDetails = $karyawan->penghasilanKaryawanDetails;

        $bonus_target = $penghasilanDetails->sum('bonus_target');
        $uang_makan = $penghasilanDetails->sum('uang_makan');
        $tunjangan_transportasi = $penghasilanDetails->sum('tunjangan_transportasi');
        $thr = $penghasilanDetails->sum('thr');
        $keterlambatan = $penghasilanDetails->sum('keterlambatan');
        $tanpa_keterangan = $penghasilanDetails->sum('tanpa_keterangan');
        $pinjaman = $penghasilanDetails->sum('pinjaman');

        $totalPenerimaan = $karyawan->gaji_pokok + $bonus_target + $uang_makan + $tunjangan_transportasi + $thr;
        $totalPotongan = $keterlambatan + $tanpa_keterangan + $pinjaman;
        $pendapatanBersih = $totalPenerimaan - $totalPotongan;

        $pdf = Pdf::loadView('pdf.slip-gaji', [
            'karyawan' => $karyawan,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'bonus_target' => $bonus_target,
            'uang_makan' => $uang_makan,
            'tunjangan_transportasi' => $tunjangan_transportasi,
            'thr' => $thr,
            'keterlambatan' => $keterlambatan,
            'tanpa_keterangan' => $tanpa_keterangan,
            'pinjaman' => $pinjaman,
            'totalPenerimaan' => $totalPenerimaan,
            'totalPotongan' => $totalPotongan,
            'pendapatanBersih' => $pendapatanBersih,
        ]);

        return $pdf->download('slip-gaji-'.$karyawan->nama.'-'.$bulan.'-'.$tahun.'.pdf');
    }
}
