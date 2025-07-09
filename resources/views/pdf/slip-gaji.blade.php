<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #000;
            font-size: 13px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .header td {
            padding: 5px 0;
        }

        .header .company-name {
            font-weight: bold;
        }

        .header .slip-title {
            text-align: right;
            font-weight: bold;
        }

        .employee-details {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .employee-details td {
            padding: 3px 5px;
        }

        .section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .section .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .section td {
            padding: 3px 5px;
        }

        .section .amount {
            text-align: right;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .summary .summary-line td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .summary td {
            padding: 3px 5px;
            font-weight: bold;
        }

        .summary .amount {
            text-align: right;
        }

        .pendapatan-bersih td {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .footer {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }

        .footer td {
            width: 50%;
            padding: 2px 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="header">
            <tr>
                <td class="company-name">CV. INTI BINTANG FORTUNA</td>
                <td class="slip-title">SLIP GAJI</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: right;">{{ strtoupper(\Carbon\Carbon::create()->month($bulan)->format('F')) }}
                    {{ $tahun }}
                </td>
            </tr>
        </table>

        <table class="employee-details">
            <tr>
                <td>NIK</td>
                <td>: {{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td>NAMA</td>
                <td>: {{ $karyawan->nama }}</td>
            </tr>
            <tr>
                <td>JABATAN</td>
                <td>: {{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td>STATUS</td>
                <td>: {{ $karyawan->status }}</td>
            </tr>
        </table>

        <table class="section">
            <tr>
                <td class="section-title" colspan="2">PENERIMAAN</td>
                <td class="section-title" colspan="2" style="padding-left: 20px;">POTONGAN</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount">{{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
                <td style="padding-left: 20px;">Keterlambatan</td>
                <td class="amount">{{ number_format($keterlambatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bonus Target</td>
                <td class="amount">{{ number_format($bonus_target, 0, ',', '.') }}</td>
                <td style="padding-left: 20px;">Tanpa Keterangan</td>
                <td class="amount">{{ number_format($tanpa_keterangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Uang Makan</td>
                <td class="amount">{{ number_format($uang_makan, 0, ',', '.') }}</td>
                <td style="padding-left: 20px;">Pinjaman</td>
                <td class="amount">{{ number_format($pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Transportasi</td>
                <td class="amount">{{ number_format($tunjangan_transportasi, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>THR</td>
                <td class="amount">{{ number_format($thr, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
        </table>

        <table class="summary">
            <tr class="summary-line">
                <td>Total Penerimaan</td>
                <td class="amount">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                <td style="padding-left: 20px;">Total Potongan</td>
                <td class="amount">{{ number_format($totalPotongan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="pendapatan-bersih">
                <td colspan="4" style="text-align: center; font-weight: bold;">Pendapatan Bersih:
                    {{ number_format($pendapatanBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="footer">
            <tr>
                <td colspan="2" style="text-align: right; padding-right: 80px; padding-bottom: 10px;">Depok, {{ now()->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Mengetahui,</td>
                <td>Penerima,</td>
            </tr>
            <tr>
                <td style="padding-top: 50px;">( Dhian Kurniasari )</td>
                <td style="padding-top: 50px;">( {{ $karyawan->nama }} )</td>
            </tr>
            <tr>
                <td>HRD Manager</td>
                <td></td>
            </tr>
        </table>
    </div>
</body>

</html>