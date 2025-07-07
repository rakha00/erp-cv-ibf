<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Invoice {{ $transaksi->no_invoice }}</title>
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
		<h1 style="text-align: center">INVOICE</h1>
		<br><br>
		<div class="info clearfix">
			<div class="left">
				<div>
					<strong>Tanggal :</strong>
					{{ \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
				</div>
				<div>
					<strong>No. Invoice :</strong>
					{{ $transaksi->no_invoice }}
				</div>
			</div>
		</div>

		<table>
			<thead>
				<tr>
					<th>Nama Barang</th>
					<th>Qty</th>
					<th>Harga Satuan</th>
					<th>Subtotal</th>
				</tr>
			</thead>
			<tbody>
				@foreach($transaksi->transaksiProdukDetails as $detail)
					<tr>
						<td>{{ $detail->sku }} {{ $detail->unitProduk->nama_unit }}</td>
						<td>{{ $detail->jumlah_keluar }}</td>
						<td>Rp{{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
						<td>Rp{{ number_format($detail->harga_jual * $detail->jumlah_keluar, 0, ',', '.') }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3" style="text-align: right; border-top:2px solid #333; padding-top:8px;">
						<strong>TOTAL</strong>
					</td>
					<td style="border-top:2px solid #333; padding-top:8px;">
						<strong>
							Rp{{ number_format($transaksi->transaksiProdukDetails->sum(fn($d) => $d->harga_jual * $d->jumlah_keluar), 0, ',', '.') }}
						</strong>
					</td>
				</tr>
			</tfoot>
		</table>


		<div class="footer">
			<div><strong>Hormat Kami,</strong></div>
			<img style="width: 17%" src="{{ public_path('images/logo-ibf.jpg') }}">
			<br>
			<br>
			<br>
			<br>
			<br>
			<div class="bank" style="text-align: left">
				<strong>Keterangan:</strong><br>
				<strong>Rekening Transfer</strong><br>

				<p style="color: red">a.n CV Inti Bintang Fortuna</p>
				<p style="color: red">Bank Panin 5602308115</p>

			</div>
		</div>
	</div>
</body>

</html>