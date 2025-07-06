<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                Your Company
                            </td>
                            <td>
                                Slip Gaji #: {{ $karyawan->id }}-{{ $bulan }}-{{ $tahun }}<br>
                                Periode: {{ \Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}<br>
                                Dicetak: {{ now()->format('d F Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Nama:</strong> {{ $karyawan->nama }}<br>
                                <strong>Jabatan:</strong> {{ $karyawan->jabatan }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Penghasilan</td>
                <td class="text-right">Jumlah</td>
            </tr>
            <tr class="item">
                <td>Gaji Pokok</td>
                <td class="text-right">Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr class="item">
                <td>Lembur</td>
                <td class="text-right">Rp {{ number_format($lembur, 0, ',', '.') }}</td>
            </tr>
            <tr class="item last">
                <td>Bonus</td>
                <td class="text-right">Rp {{ number_format($bonus, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td class="text-right"><strong>Total Gaji: Rp {{ number_format($totalGaji, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="heading">
                <td>Potongan</td>
                <td class="text-right">Jumlah</td>
            </tr>
            <tr class="item last">
                <td>Kasbon</td>
                <td class="text-right">Rp {{ number_format($kasbon, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td class="text-right"><strong>Gaji Diterima: Rp {{ number_format($gajiDiterima, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>