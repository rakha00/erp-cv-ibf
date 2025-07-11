<?php

use App\Filament\Widgets\PiutangList;
use App\Models\Piutang;
use App\Models\TransaksiProduk;
use function Pest\Livewire\livewire;

describe('PiutangList Widget', function () {
	it('displays only outstanding and partially paid receivables', function () {
		// Create some dummy data
		$transaksiProduk1 = TransaksiProduk::factory()->create(['no_invoice' => 'INV-001']);
		$transaksiProduk2 = TransaksiProduk::factory()->create(['no_invoice' => 'INV-002']);
		$transaksiProduk3 = TransaksiProduk::factory()->create(['no_invoice' => 'INV-003']);

		Piutang::factory()->create([
			'transaksi_produk_id' => $transaksiProduk1->id,
			'status_pembayaran' => 'belum lunas',
			'jatuh_tempo' => now()->addDays(5),
		]);
		Piutang::factory()->create([
			'transaksi_produk_id' => $transaksiProduk2->id,
			'status_pembayaran' => 'tercicil',
			'jatuh_tempo' => now()->addDays(10),
		]);
		Piutang::factory()->create([
			'transaksi_produk_id' => $transaksiProduk3->id,
			'status_pembayaran' => 'sudah lunas', // This should not be displayed
			'jatuh_tempo' => now()->addDays(15),
		]);

		livewire(PiutangList::class)
			->assertSeeHtml('INV-001')
			->assertSeeHtml('belum lunas')
			->assertSeeHtml('INV-002')
			->assertSeeHtml('tercicil')
			->assertDontSeeHtml('INV-003');
	});

	it('displays correct number of columns', function () {
		$widget = new PiutangList();
		expect($widget->getColumns())->toBe(2);
	});

	it('displays correct heading', function () {
		livewire(PiutangList::class)
			->assertSeeHtml('Daftar Piutang Belum Lunas / Tercicil');
	});
});