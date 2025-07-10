<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Invoice {{ $transaksi->no_invoice }}</title>
</head>

<body
	style="font-family: Arial, sans-serif; font-size: 15px; margin: 0; padding: 0; width: 100%; box-sizing: border-box;">
	<div style="width: 100%; padding-bottom: 0; margin-bottom: 0;">
		<table
			style="width: 100%; border-collapse: collapse; border: none !important; padding: 0 !important; margin: 0 !important;">
			<tr>
				<td
					style="width: 80px; border: none; vertical-align: middle; padding: 0 !important; margin: 0 !important;">
					<img src="{{ public_path('images/logo-ibf.jpg') }}" alt="Logo"
						style="width: 100px; margin-right: 0;">
				</td>
				<td
					style="border: none; text-align: center; vertical-align: middle; padding: 0 !important; margin: 0 !important;">
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
		style="border: none; border-bottom: 4px solid #000000; margin: 0 -5000px !important; padding: 0 !important; width: 10000px !important;">
	<hr
		style="border: none; border-bottom: 4px solid #f0c420; margin: 0 -5000px !important; padding: 0 !important; width: 10000px !important;">
	<div style="padding: 0 60px;">
		<h1 style="text-align: center">INVOICE</h1>
		<br><br>
		<div style="margin-bottom: 10px;">
			<div style="float: left; width: 50%;">
				<div>
					<strong>Tanggal :</strong>
					{{ \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
				</div>
				<div>
					<strong>No. Invoice :</strong>
					{{ $transaksi->no_invoice }}
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>

		<table style="width: 100%; border-collapse: collapse; margin-top: 0;">
			<thead>
				<tr>
					<th style="border: 1px solid #333; padding: 4px; text-align: left;">Nama Barang</th>
					<th style="border: 1px solid #333; padding: 4px; text-align: left;">Qty</th>
					<th style="border: 1px solid #333; padding: 4px; text-align: left;">Harga Satuan</th>
					<th style="border: 1px solid #333; padding: 4px; text-align: left;">Subtotal</th>
				</tr>
			</thead>
			<tbody>
				@foreach($transaksi->transaksiProdukDetails as $detail)
					<tr>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">{{ $detail->sku }}
							{{ $detail->unitProduk->nama_unit }}
						</td>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">{{ $detail->jumlah_keluar }}
						</td>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">
							Rp{{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">
							Rp{{ number_format($detail->harga_jual * $detail->jumlah_keluar, 0, ',', '.') }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3"
						style="border: 1px solid #333; padding: 4px; text-align: right; border-top:2px solid #333; padding-top:8px;">
						<strong>TOTAL</strong>
					</td>
					<td
						style="border: 1px solid #333; padding: 4px; text-align: left; border-top:2px solid #333; padding-top:8px;">
						<strong>
							Rp{{ number_format($transaksi->transaksiProdukDetails->sum(fn($d) => $d->harga_jual * $d->jumlah_keluar), 0, ',', '.') }}
						</strong>
					</td>
				</tr>
			</tfoot>
		</table>

		<div style="margin-top: 60px; text-align: right;">
			<div><strong>Hormat Kami,</strong></div>
			<img style="width: 17%" src="{{ public_path('images/logo-ibf.jpg') }}">
			<br><br><br><br><br>
			<div style="margin-top: 20px; text-align: left;">
				<strong>Keterangan:</strong><br>
				<strong>Rekening Transfer</strong><br>
				<p style="color: red">a.n CV Inti Bintang Fortuna</p>
				<p style="color: red">Bank Panin 5602308115</p>
			</div>
		</div>
	</div>
</body>

</html>