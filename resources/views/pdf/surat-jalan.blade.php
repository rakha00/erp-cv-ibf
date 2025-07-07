<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Surat Jalan</title>
	<style>
		@page {
			margin: 0;
		}

		html,
		body {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: Arial, sans-serif;
			font-size: 15px;
			margin: 0;
			padding: 0;
			width: 100%;
		}

		.header {
			width: 100%;
			border-bottom: 2px solid #000;
			padding-bottom: 10px;
			margin-bottom: 10px;
		}

		.logo {
			width: 100px;
		}

		.company-info {
			text-align: center;
			font-size: 14px;
			line-height: 1.4;
		}

		.company-info b {
			font-size: 15px;
		}

		.info {
			margin-bottom: 10px;
		}

		.info .left {
			float: left;
			width: 50%;
		}

		.info .right {
			float: right;
			width: 50%;
			text-align: right;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 0;
		}

		th,
		td {
			border: 1px solid #333;
			padding: 4px;
			text-align: left;
		}

		.header-table,
		.header-table td,
		.header-table th {
			border: none !important;
			padding: 0 !important;
			margin: 0 !important;
		}

		.total {
			text-align: right;
			margin-top: 10px;
		}

		.footer {
			margin-top: 60px;
			text-align: right
		}

		.footer .bank {
			margin-top: 20px;
		}

		.clearfix::after {
			content: "";
			clear: both;
			display: table;
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

		.signature-label {
			font-weight: bold;
			font-size: 15px;
		}
	</style>
</head>

<body>
	<div class="header" style="margin-bottom: 0; padding-bottom: 0;">
		<table style="width: 100%; border-collapse: collapse;">
			<tr>
				<td style="width: 80px; border: none; vertical-align: middle;">
					<img src="{{ public_path('images/logo-ibf.jpg') }}" alt="Logo"
						style="width: 100px; margin-right: 0;">
				</td>
				<td style="border: none; text-align: center; vertical-align: middle;">
					<div style="font-size: 36px; font-weight: bold; line-height: 1;">CV. INTI BINTANG FORTUNA</div>
					<div style="font-size: 20px; font-weight: bold; line-height: 1;">Pendingin & Tata Udara - Civil -
						Electrical</div>
					<div style="font-size: 12px; line-height: 1.2;">Jl. Raja Indra Kel. Labuhan Dalam Kec. Tanjung
						Senang - Bandar Lampung Kode Pos : 35141</div>
					<div style="font-size: 12px; line-height: 1.2;">No. Telp : 0821-8416-2241 E-mail :
						cvintibintangfortuna@gmail.com</div>
				</td>
			</tr>
		</table>
	</div>
	<hr
		style="border: none; border-bottom: 4px solid #f0c420; margin: 0 -5000px !important; padding: 0 !important; width: 10000px !important;">
	<div class="content-wrapper" style="padding: 0 60px;">
		<h1 style="text-align: center">SURAT JALAN</h1>
		<br><br>
		<div class="info clearfix">
			<div class="left">
				<div>
					<strong>Tanggal :</strong>
					{{ \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
				</div>
				<div>
					<strong>No. Surat Jalan :</strong>
					{{ $transaksi->no_invoice }}
				</div>
			</div>
			{{-- <div class="right">
				<strong>Kepada:</strong><br>
			</div> --}}
		</div>

		<table style="margin-top: 20px">
			<thead>
				<tr>
					<th style="width: 15%; text-align:center">QTY</th>
					<th style="text-align:center">NAMA BARANG</th>
					<th style="width: 30%; text-align:center">REMARKS</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($transaksi->transaksiProdukDetails as $detail)
					<tr>
						<td style="text-align:center">{{ $detail->jumlah_keluar }}</td>
						<td>{{ $detail->sku }} {{ $detail->unitProduk->nama_unit }}</td>
						<td>{{ $detail->remarks ?? '-' }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="signature-section clearfix" style="margin-top: 80px">
			<div class="signature-box">
				<div class="signature-label">Tanda Terima</div>
				<div
					style="margin-top: 80px; border-bottom:1px solid black; width: 70%; margin-left: auto; margin-right: auto">
				</div>
			</div>

			<div class="signature-box">
				<div class="signature-label">Hormat Kami</div>
				<img style="width: 40%; margin-top: 10px" src="{{ public_path('images/logo-ibf.jpg') }}">
			</div>
		</div>
	</div>
</body>

</html>