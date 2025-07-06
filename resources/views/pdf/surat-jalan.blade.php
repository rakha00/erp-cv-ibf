<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Surat Jalan</title>
	<style>
		@page {
			size: A4;
			margin: 20mm;
		}

		body {
			font-family: Arial, sans-serif;
			font-size: 12px;
			line-height: 1.4;
			margin-left: 20px;
			margin-right: 20px;
			padding: 0;
			color: #333;
		}

		.header {
			width: 100%;
			border-bottom: 2px solid #000;
			padding-bottom: 10px;
			margin-bottom: 10px;
		}

		.logo {
			width: 80px;
		}

		.company-info {
			text-align: center;
			font-size: 12px;
		}

		.company-info b {
			font-size: 14px;
		}

		.company-name {
			font-size: 21px;
			font-weight: bold;
			margin-bottom: 5px;
			color: #000;
		}

		.company-tagline {
			font-size: 14px;
			color: #666;
			margin-bottom: 15px;
		}

		.document-title {
			font-size: 16px;
			font-weight: bold;
			margin: 0;
			color: #000;
		}

		.info-section {
			margin-bottom: 25px;
		}

		.info-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}

		.info-table td {
			padding: 5px 0;
			border: none;
			vertical-align: top;
		}

		.info-label {
			font-weight: bold;
			width: 120px;
			text-align: left;
		}

		.info-colon {
			width: 10px;
			text-align: left;
		}

		.info-value {
			text-align: left;
			line-height: 1.3;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			font-size: 14px;
		}

		th {
			background-color: #f8f9fa;
			font-weight: bold;
			padding: 10px 8px;
			border: 1px solid #333;
			text-align: center;
		}

		td {
			padding: 8px;
			border: 1px solid #333;
			text-align: center;
			vertical-align: middle;
		}

		/* Override untuk header table - hapus semua border dan styling */
		.header-table,
		.header-table td,
		.header-table th {
			border: none !important;
			padding: 0 !important;
			margin: 0 !important;
			background-color: transparent !important;
		}

		.qty-col {
			width: 10%;
		}

		.item-col {
			width: 60%;
		}

		.remarks-col {
			width: 30%;
		}

		.signature-section {
			margin-top: 40px;
			width: 100%;
		}

		.signature-box {
			text-align: center;
			width: 45%;
			float: left;
			margin: 0 2.5%;
		}

		.signature-line {
			border-bottom: 1px solid #333;
			height: 60px;
			margin-bottom: 10px;
		}

		.signature-label {
			font-weight: bold;
			font-size: 15px;
		}

		/* Print specific styles */
		@media print {
			body {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.header {
				page-break-after: avoid;
			}

			table {
				page-break-inside: avoid;
			}

			.signature-section {
				page-break-before: avoid;
			}
		}
	</style>
</head>

<body>
	{{-- HEADER DENGAN LOGO --}}
	<table class="header header-table">
		<tr>
			<td style="width: 100px;">
				<img src="{{ public_path('logo.jpg') }}" class="logo">
			</td>
			<td class="company-info">
				<b>PT ALPHA PUTRA SINERGI</b><br>
				Jl. Rajawali Selatan Raya Rukan Multi Guna Kemayoran Blok C No. 2M, Pademangan -<br>
				Jakarta 14410 <br>
				Tlp: (021) 38880711 • Whatsapp 0821-1839-0606<br>
				Email : admin@alphaputrasinergi.com | Website: www.alphaputrasinergi.com

				</div>
		</tr>
	</table>
	<hr>
	<br>
	<h1 style="text-align: center">SURAT JALAN</h1>
	<div class="info-section">
		<table class="info-table">
			<tr>
				<td class="info-label">Tanggal</td>
				<td class="info-colon">:</td>
				<td class="info-value">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</td>
			</tr>
			<tr>
				<td class="info-label">Kepada</td>
				<td class="info-colon">:</td>
				<td class="info-value">
					{{-- {{ $transaksi->toko->nama_konsumen }}<br>
					{{ $transaksi->toko->alamat ?? '' }} --}}
				</td>
			</tr>
			<tr>
				<td class="info-label">No. Surat Jalan</td>
				<td class="info-colon">:</td>
				<td class="info-value">{{ $transaksi->no_invoice }}</td>
			</tr>
		</table>
	</div>
	<br>
	<table>
		<thead>
			<tr>
				<th class="qty-col">QTY</th>
				<th class="item-col">NAMA BARANG</th>
				<th class="remarks-col">REMARKS</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($transaksi->transaksiProdukDetails as $detail)
				<tr>
					<td>{{ $detail->jumlah_keluar }}</td>
					<td>{{ $detail->sku }} {{ $detail->nama_unit }}</td>
					<td>{{ $detail->remarks ?? '-' }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<div class="signature-section">
		<div class="signature-box">
			<div class="signature-label">Tanda Terima</div>
		</div>

		<div class="signature-box">
			<div class="signature-label">Hormat Kami</div>
			<img style="width: 30%" src="{{ public_path('stampel-aps.jpg') }}">
		</div>
	</div>
</body>

</html>