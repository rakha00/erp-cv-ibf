<?php

use App\Filament\Widgets\AsetKaryawanOverview;
use App\Models\Aset;
use App\Models\Karyawan;
use function Pest\Livewire\livewire;

describe('Aset and Karyawan Overview', function () {
	it('displays total asset value and total employee count', function () {
		// Create dummy data using factories
		Aset::factory()->create(['harga' => 1000000]);
		Aset::factory()->create(['harga' => 2500000]);
		Karyawan::factory()->count(5)->create();

		livewire(AsetKaryawanOverview::class)
			->assertSeeHtml('Total Nilai Aset')
			->assertSeeHtml('Rp 3.500.000')
			->assertSeeHtml('Jumlah Karyawan')
			->assertSeeHtml('5');
	});

	it('handles zero assets and employees', function () {
		// No dummy data created, so counts should be zero

		livewire(AsetKaryawanOverview::class)
			->assertSeeHtml('Total Nilai Aset')
			->assertSeeHtml('Rp 0')
			->assertSeeHtml('Jumlah Karyawan')
			->assertSeeHtml('0');
	});

	it('displays correct column count', function () {
		// This test doesn't require Livewire component rendering as getColumns is a static method
		$widget = new AsetKaryawanOverview();
		expect($widget->getColumns())->toBe(2);
	});
});