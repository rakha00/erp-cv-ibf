<?php

use App\Filament\Widgets\TransaksiBarangOverview;
use App\Models\BarangMasukDetail;
use App\Models\TransaksiProdukDetail;
use function Pest\Livewire\livewire;

describe('TransaksiBarangOverview Widget', function () {
	it('displays the correct total product transactions and total incoming goods', function () {
		// Create dummy data for TransaksiProdukDetail
		TransaksiProdukDetail::factory()->create([
			'harga_jual' => 100000,
			'jumlah_keluar' => 5,
		]); // Total: 500,000
		TransaksiProdukDetail::factory()->create([
			'harga_jual' => 200000,
			'jumlah_keluar' => 3,
		]); // Total: 600,000

		// Create dummy data for BarangMasukDetail
		BarangMasukDetail::factory()->create([
			'harga_modal' => 50000,
			'jumlah_barang_masuk' => 10,
		]); // Total: 500,000
		BarangMasukDetail::factory()->create([
			'harga_modal' => 75000,
			'jumlah_barang_masuk' => 4,
		]); // Total: 300,000

		$expectedTotalTransaksiProduk = 'Rp 1.100.000'; // 500,000 + 600,000
		$expectedTotalBarangMasuk = 'Rp 800.000'; // 500,000 + 300,000

		livewire(TransaksiBarangOverview::class)
			->assertSeeHtml('Total Transaksi Produk')
			->assertSeeHtml($expectedTotalTransaksiProduk)
			->assertSeeHtml('Total Barang Masuk')
			->assertSeeHtml($expectedTotalBarangMasuk);
	});

	it('handles zero product transactions and incoming goods correctly', function () {
		// No dummy data created, so totals should be zero

		livewire(TransaksiBarangOverview::class)
			->assertSeeHtml('Total Transaksi Produk')
			->assertSeeHtml('Rp 0')
			->assertSeeHtml('Total Barang Masuk')
			->assertSeeHtml('Rp 0');
	});

	it('displays correct number of columns', function () {
		$widget = new TransaksiBarangOverview();
		expect($widget->getColumns())->toBe(2);
	});
});