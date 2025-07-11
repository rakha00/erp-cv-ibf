<?php

use App\Filament\Widgets\UtangList;
use App\Models\Utang;
use App\Models\BarangMasuk;
use function Pest\Livewire\livewire;

describe('UtangList Widget', function () {
	it('displays only outstanding and partially paid debts', function () {
		// Create some dummy data
		$barangMasuk1 = BarangMasuk::factory()->create(['nomor_barang_masuk' => 'BM-001']);
		$barangMasuk2 = BarangMasuk::factory()->create(['nomor_barang_masuk' => 'BM-002']);
		$barangMasuk3 = BarangMasuk::factory()->create(['nomor_barang_masuk' => 'BM-003']);

		Utang::factory()->create([
			'barang_masuk_id' => $barangMasuk1->id,
			'status_pembayaran' => 'belum lunas',
			'jatuh_tempo' => now()->addDays(5),
		]);
		Utang::factory()->create([
			'barang_masuk_id' => $barangMasuk2->id,
			'status_pembayaran' => 'tercicil',
			'jatuh_tempo' => now()->addDays(10),
		]);
		Utang::factory()->create([
			'barang_masuk_id' => $barangMasuk3->id,
			'status_pembayaran' => 'sudah lunas', // This should not be displayed
			'jatuh_tempo' => now()->addDays(15),
		]);

		livewire(UtangList::class)
			->assertSeeHtml('BM-001')
			->assertSeeHtml('belum lunas')
			->assertSeeHtml('BM-002')
			->assertSeeHtml('tercicil')
			->assertDontSeeHtml('BM-003');
	});

	it('displays correct number of columns', function () {
		$widget = new UtangList();
		expect($widget->getColumns())->toBe(2);
	});

	it('displays correct heading', function () {
		livewire(UtangList::class)
			->assertSeeHtml('Daftar Utang Belum Lunas / Tercicil');
	});
});