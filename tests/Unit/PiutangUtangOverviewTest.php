<?php

use App\Filament\Widgets\PiutangUtangOverview;
use App\Models\Piutang;
use App\Models\Utang;
use function Pest\Livewire\livewire;

describe('PiutangUtangOverview Widget', function () {
	it('displays the correct total piutang and utang values', function () {
		// Create dummy data for Piutang
		Piutang::factory()->create([
			'total_harga_modal' => 5000000,
			'sudah_dibayar' => 1000000,
			'status_pembayaran' => 'belum lunas',
		]);
		Piutang::factory()->create([
			'total_harga_modal' => 3000000,
			'sudah_dibayar' => 500000,
			'status_pembayaran' => 'tercicil',
		]);
		// This one should not be counted as it's 'sudah lunas'
		Piutang::factory()->create([
			'total_harga_modal' => 2000000,
			'sudah_dibayar' => 2000000,
			'status_pembayaran' => 'sudah lunas',
		]);

		// Create dummy data for Utang
		Utang::factory()->create([
			'total_harga_modal' => 7000000,
			'sudah_dibayar' => 2000000,
			'status_pembayaran' => 'belum lunas',
		]);
		Utang::factory()->create([
			'total_harga_modal' => 4000000,
			'sudah_dibayar' => 1000000,
			'status_pembayaran' => 'tercicil',
		]);
		// This one should not be counted as it's 'sudah lunas'
		Utang::factory()->create([
			'total_harga_modal' => 1000000,
			'sudah_dibayar' => 1000000,
			'status_pembayaran' => 'sudah lunas',
		]);

		// Expected total piutang: (5M - 1M) + (3M - 0.5M) = 4M + 2.5M = 6.5M
		$expectedTotalPiutang = 'Rp 6.500.000';
		// Expected total utang: (7M - 2M) + (4M - 1M) = 5M + 3M = 8M
		$expectedTotalUtang = 'Rp 8.000.000';

		livewire(PiutangUtangOverview::class)
			->assertSeeHtml('Total Piutang')
			->assertSeeHtml($expectedTotalPiutang)
			->assertSeeHtml('Total Utang')
			->assertSeeHtml($expectedTotalUtang);
	});

	it('handles zero piutang and utang correctly', function () {
		// No dummy data created, so totals should be zero

		livewire(PiutangUtangOverview::class)
			->assertSeeHtml('Total Piutang')
			->assertSeeHtml('Rp 0')
			->assertSeeHtml('Total Utang')
			->assertSeeHtml('Rp 0');
	});

	it('displays correct number of columns', function () {
		$widget = new PiutangUtangOverview();
		expect($widget->getColumns())->toBe(2);
	});
});