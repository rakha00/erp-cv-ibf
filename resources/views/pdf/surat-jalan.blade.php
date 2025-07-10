<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Surat Jalan</title>
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
		<h1 style="text-align: center">SURAT JALAN</h1>
		<br><br>
		<div style="margin-bottom: 10px;">
			<div style="float: left; width: 50%;">
				<div>
					<strong>Tanggal :</strong>
					{{ \Illuminate\Support\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}
				</div>
				<div>
					<strong>No. Surat Jalan :</strong>
					{{ $transaksi->no_invoice }}
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>

		<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
			<thead>
				<tr>
					<th style="width: 15%; text-align:center; border: 1px solid #333; padding: 4px;">QTY</th>
					<th style="text-align:center; border: 1px solid #333; padding: 4px;">NAMA BARANG</th>
					<th style="width: 30%; text-align:center; border: 1px solid #333; padding: 4px;">REMARKS</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($transaksi->transaksiProdukDetails as $detail)
					<tr>
						<td style="text-align:center; border: 1px solid #333; padding: 4px;">{{ $detail->jumlah_keluar }}
						</td>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">{{ $detail->sku }}
							{{ $detail->unitProduk->nama_unit }}
						</td>
						<td style="border: 1px solid #333; padding: 4px; text-align: left;">{{ $detail->remarks ?? '-' }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div style="margin-top: 80px; width: 100%;">
			<div style="text-align: center; width: 45%; float: left; margin: 0 2.5%;">
				<div style="font-weight: bold; font-size: 15px;">Tanda Terima</div>
				<div
					style="margin-top: 80px; border-bottom:1px solid black; width: 70%; margin-left: auto; margin-right: auto;">
				</div>
			</div>

			<div style="text-align: center; width: 45%; float: left; margin: 0 2.5%;">
				<div style="font-weight: bold; font-size: 15px;">Hormat Kami</div>
				<img style="width: 40%; margin-top: 10px" src="{{ public_path('images/logo-ibf.jpg') }}">
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</body>

</html>