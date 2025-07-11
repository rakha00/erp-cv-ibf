<?php

use App\Filament\Resources\TransaksiProdukResource;
use App\Models\TransaksiProduk;
use App\Models\UnitProduk;
use App\Models\TransaksiProdukDetail;
use Carbon\Carbon;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

describe('TransaksiProduk Model', function () {
	it('has the correct fillable attributes', function () {
		$transaksiProduk = new TransaksiProduk();
		$fillable = ['no_invoice', 'no_surat_jalan', 'tanggal', 'remarks'];
		expect($transaksiProduk->getFillable())->toEqual($fillable);
	});

	it('uses SoftDeletes trait', function () {
		$transaksiProduk = new TransaksiProduk();
		expect(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses($transaksiProduk)))->toBeTrue();
	});

	it('has transaksiProdukDetails relationship', function () {
		$transaksiProduk = TransaksiProduk::factory()->create();
		TransaksiProdukDetail::factory()->count(2)->create(['transaksi_produk_id' => $transaksiProduk->id]);

		expect($transaksiProduk->transaksiProdukDetails)->toHaveCount(2);
		expect($transaksiProduk->transaksiProdukDetails->first())->toBeInstanceOf(TransaksiProdukDetail::class);
	});
});

describe('TransaksiProdukResource Form Logic', function () {
	it('generates invoice and delivery note numbers correctly for the first record of the day', function () {
		DB::shouldReceive('transaction')
			->once()
			->andReturnUsing(function ($callback) {
				$callback();
			});

		TransaksiProduk::query()->forceDelete();

		$mockSet = Mockery::mock(Set::class);
		$testDate = '2025-01-15';
		$expectedInvoice = 'INV/15012025-1';
		$expectedDeliveryNote = 'SJ/15012025-1';

		$mockSet->shouldReceive('__invoke')
			->with('no_invoice', $expectedInvoice)
			->once();
		$mockSet->shouldReceive('__invoke')
			->with('no_surat_jalan', $expectedDeliveryNote)
			->once();

		$method = new ReflectionMethod(TransaksiProdukResource::class, 'generateInvoiceAndDeliveryNoteNumbers');
		$method->setAccessible(true);
		$method->invoke(null, $testDate, $mockSet);
	});

	it('generates invoice and delivery note numbers correctly for subsequent records of the day', function () {
		TransaksiProduk::factory()->create([
			'tanggal' => '2025-01-15',
			'no_invoice' => 'INV/15012025-10',
			'no_surat_jalan' => 'SJ/15012025-10',
		]);

		DB::shouldReceive('transaction')
			->once()
			->andReturnUsing(function ($callback) {
				$callback();
			});

		$mockSet = Mockery::mock(Set::class);
		$testDate = '2025-01-15';
		$expectedInvoice = 'INV/15012025-11';
		$expectedDeliveryNote = 'SJ/15012025-11';

		$mockSet->shouldReceive('__invoke')
			->with('no_invoice', $expectedInvoice)
			->once();
		$mockSet->shouldReceive('__invoke')
			->with('no_surat_jalan', $expectedDeliveryNote)
			->once();

		$method = new ReflectionMethod(TransaksiProdukResource::class, 'generateInvoiceAndDeliveryNoteNumbers');
		$method->setAccessible(true);
		$method->invoke(null, $testDate, $mockSet);
	});

	it('calculates total_harga_jual correctly for a TransaksiProduk record', function () {
		$transaksiProduk = TransaksiProduk::factory()->create();
		TransaksiProdukDetail::factory()->create([
			'transaksi_produk_id' => $transaksiProduk->id,
			'harga_jual' => 100000,
			'jumlah_keluar' => 5,
		]);
		TransaksiProdukDetail::factory()->create([
			'transaksi_produk_id' => $transaksiProduk->id,
			'harga_jual' => 200000,
			'jumlah_keluar' => 3,
		]);

		$method = new ReflectionMethod(TransaksiProdukResource::class, 'calculateTotalHargaJual');
		$method->setAccessible(true);
		$total = $method->invoke(null, $transaksiProduk);

		expect($total)->toBe(1100000.0); // 100k*5 + 200k*3 = 500k + 600k = 1.1M
	});

	it('calculates total_keuntungan correctly for a TransaksiProduk record', function () {
		$transaksiProduk = TransaksiProduk::factory()->create();
		$unitProduk1 = UnitProduk::factory()->create(['harga_modal' => 50000]);
		$unitProduk2 = UnitProduk::factory()->create(['harga_modal' => 100000]);

		TransaksiProdukDetail::factory()->create([
			'transaksi_produk_id' => $transaksiProduk->id,
			'unit_produk_id' => $unitProduk1->id,
			'harga_jual' => 100000,
			'jumlah_keluar' => 5,
		]); // Keuntungan: (100k - 50k) * 5 = 250k
		TransaksiProdukDetail::factory()->create([
			'transaksi_produk_id' => $transaksiProduk->id,
			'unit_produk_id' => $unitProduk2->id,
			'harga_jual' => 200000,
			'jumlah_keluar' => 3,
		]); // Keuntungan: (200k - 100k) * 3 = 300k

		$method = new ReflectionMethod(TransaksiProdukResource::class, 'calculateTotalKeuntungan');
		$method->setAccessible(true);
		$total = $method->invoke(null, $transaksiProduk);

		expect($total)->toBe(550000.0); // 250k + 300k = 550k
	});
});

describe('TransaksiProduk Filament Resource', function () {






});