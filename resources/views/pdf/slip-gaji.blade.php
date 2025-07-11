<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
</head>

<body style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #000; font-size: 13px;">
    <div style="max-width: 800px; margin: auto; padding: 20px;">
        <table style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 5px;">
            <tr>
                <td style="padding: 5px 0; font-weight: bold;">CV. INTI BINTANG FORTUNA</td>
                <td style="padding: 5px 0; text-align: right; font-weight: bold;">SLIP GAJI</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"></td>
                <td style="padding: 5px 0; text-align: right;">
                    {{ strtoupper(\Carbon\Carbon::create()->month($bulan)->format('F')) }} {{ $tahun }}
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="padding: 3px 5px;">NIK</td>
                <td style="padding: 3px 5px;">: {{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">NAMA</td>
                <td style="padding: 3px 5px;">: {{ $karyawan->nama }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">JABATAN</td>
                <td style="padding: 3px 5px;">: {{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">STATUS</td>
                <td style="padding: 3px 5px;">: {{ $karyawan->status }}</td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
            <tr>
                <td colspan="2"
                    style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; padding: 3px 5px;">
                    PENERIMAAN</td>
                <td colspan="2"
                    style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; padding: 3px 5px; padding-left: 20px;">
                    POTONGAN</td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 3px 5px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">Gaji Pokok</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}
                </td>
                <td style="padding: 3px 5px; padding-left: 20px;">Keterlambatan</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($keterlambatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">Bonus Target</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($bonus_target, 0, ',', '.') }}</td>
                <td style="padding: 3px 5px; padding-left: 20px;">Tanpa Keterangan</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($tanpa_keterangan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">Uang Makan</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($uang_makan, 0, ',', '.') }}</td>
                <td style="padding: 3px 5px; padding-left: 20px;">Pinjaman</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">Tunjangan Transportasi</td>
                <td style="padding: 3px 5px; text-align: right;">
                    {{ number_format($tunjangan_transportasi, 0, ',', '.') }}
                </td>
                <td style="padding: 3px 5px;"></td>
                <td style="padding: 3px 5px;"></td>
            </tr>
            <tr>
                <td style="padding: 3px 5px;">THR</td>
                <td style="padding: 3px 5px; text-align: right;">{{ number_format($thr, 0, ',', '.') }}</td>
                <td style="padding: 3px 5px;"></td>
                <td style="padding: 3px 5px;"></td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 3px 5px;">&nbsp;</td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 0;">
            <tr style="font-weight: bold;">
                <td style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 5px;">Total
                    Penerimaan</td>
                <td
                    style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 5px; text-align: right;">
                    {{ number_format($totalPenerimaan, 0, ',', '.') }}
                </td>
                <td
                    style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 5px; padding-left: 20px;">
                    Total Potongan</td>
                <td
                    style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 5px; text-align: right;">
                    {{ number_format($totalPotongan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 3px 5px; font-weight: bold;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4"
                    style="border-bottom: 3px double #000; padding-bottom: 10px; text-align: center; font-weight: bold; padding: 3px 5px;">
                    Pendapatan Bersih: {{ number_format($pendapatanBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-top: 20px; text-align: center;">
            <tr>
                <td colspan="2"
                    style="width: 50%; padding: 2px 5px; text-align: right; padding-right: 80px; padding-bottom: 10px;">
                    Depok, {{ now()->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 2px 5px;">Mengetahui,</td>
                <td style="width: 50%; padding: 2px 5px;">Penerima,</td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 2px 5px; padding-top: 50px;">( - )</td>
                <td style="width: 50%; padding: 2px 5px; padding-top: 50px;">( {{ $karyawan->nama }} )</td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 2px 5px;">HRD Manager</td>
                <td style="width: 50%; padding: 2px 5px;"></td>
            </tr>
        </table>
    </div>
</body>

</html>