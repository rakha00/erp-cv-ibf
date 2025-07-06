<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Invoice {{ $transaksi->no_invoice }}</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			font-size: 15px;
			margin-left: 60px;
			margin-right: 60px;
			margin-top: 20px;
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
			/* Diperbesar dari 12px */
			line-height: 1.4;
		}

		.company-info b {
			font-size: 15px;
			/* Diperbesar dari 14px */
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
			margin-top: 40px;
		}

		th,
		td {
			border: 1px solid #333;
			padding: 4px;
			text-align: left;
		}

		/* Override untuk header table - hapus semua border dan styling */
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
	<h1 style="text-align: center">INVOICE</h1>
	<br><br>
	<div class="info clearfix">
		<div class="left">
			<div>
				<strong>Tanggal :</strong>
				{{ \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
			</div>
			<div>
				{{-- <strong>Kepada :</strong> {{ $transaksi->toko->nama_konsumen }}<br>{{ $transaksi->toko->alamat ??
				'' }} --}}
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
					<td>{{ $detail->sku }} {{ $detail->nama_unit }}</td>
					<td>{{ $detail->jumlah_keluar }}</td>
					<td>Rp{{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
					<td>Rp{{ number_format($detail->harga_jual * $detail->jumlah_keluar, 0, ',', '.') }}</td>
				</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				{{-- colspan 3: nama barang, qty, harga satuan --}}
				<td colspan="3" style="text-align: right; border-top:2px solid #333; padding-top:8px;">
					<strong>TOTAL</strong>
				</td>
				{{-- kolom subtotal --}}
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
		<img style="width: 17%" src="{{ public_path('stampel-aps.jpg') }}">
		<br>
		<br>
		<br>
		<br>
		<br>
		<div class="bank" style="text-align: left">
			<strong>Keterangan:</strong><br>
			<strong>Rekening Transfer</strong><br>

			<p style="color: red">a.n Alpha Putra Sinergi</p>
			<p style="color: red">Bank Mandiri 101.00199.0000.8</p>

		</div>
	</div>
</body>

</html>