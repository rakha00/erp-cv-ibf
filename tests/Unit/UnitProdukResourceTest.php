<?php

use App\Filament\Resources\UnitProdukResource;
use App\Models\BarangMasukDetail;
use App\Models\TransaksiProdukDetail;
use App\Models\UnitProduk;
use Illuminate\Database\Eloquent\SoftDeletes;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

describe('UnitProduk Model', function () {
	it('uses the HasFactory and SoftDeletes traits', function () {
		$unitProduk = new UnitProduk();
		expect(in_array(SoftDeletes::class, class_uses($unitProduk)))->toBeTrue();
		expect(in_array(\Illuminate\Database\Eloquent\Factories\HasFactory::class, class_uses($unitProduk)))->toBeTrue();
	});

	it('has the correct fillable attributes', function () {
		$unitProduk = new UnitProduk();
		$fillable = ['sku', 'nama_unit', 'harga_modal', 'stok_awal', 'remarks'];
		expect($unitProduk->getFillable())->toEqual($fillable);
	});

	it('can be soft deleted', function () {
		$unitProduk = UnitProduk::factory()->create();
		$unitProduk->delete();
		assertSoftDeleted($unitProduk);
	});

	it('has barangMasukDetails relationship', function () {
		$unitProduk = UnitProduk::factory()->create();
		BarangMasukDetail::factory()->count(2)->create(['unit_produk_id' => $unitProduk->id]);

		expect($unitProduk->barangMasukDetails)->toHaveCount(2);
		expect($unitProduk->barangMasukDetails->first())->toBeInstanceOf(BarangMasukDetail::class);
	});

	it('has transaksiProdukDetails relationship', function () {
		$unitProduk = UnitProduk::factory()->create();
		TransaksiProdukDetail::factory()->count(3)->create(['unit_produk_id' => $unitProduk->id]);

		expect($unitProduk->transaksiProdukDetails)->toHaveCount(3);
		expect($unitProduk->transaksiProdukDetails->first())->toBeInstanceOf(TransaksiProdukDetail::class);
	});
});

describe('UnitProduk Resource Calculations', function () {
	it('calculates stok_masuk correctly', function () {
		$unitProduk = UnitProduk::factory()->create();
		BarangMasukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_barang_masuk' => 10]);
		BarangMasukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_barang_masuk' => 5]);

		// Access the private static method using reflection
		$method = new ReflectionMethod(UnitProdukResource::class, 'calculateStokMasuk');
		$method->setAccessible(true);
		$stokMasuk = $method->invoke(null, $unitProduk);

		expect($stokMasuk)->toBe(15);
	});

	it('calculates stok_keluar correctly', function () {
		$unitProduk = UnitProduk::factory()->create();
		TransaksiProdukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_keluar' => 7]);
		TransaksiProdukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_keluar' => 3]);

		// Access the private static method using reflection
		$method = new ReflectionMethod(UnitProdukResource::class, 'calculateStokKeluar');
		$method->setAccessible(true);
		$stokKeluar = $method->invoke(null, $unitProduk);

		expect($stokKeluar)->toBe(10);
	});

	it('calculates stok_akhir correctly', function () {
		$unitProduk = UnitProduk::factory()->create(['stok_awal' => 100]);
		BarangMasukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_barang_masuk' => 20]);
		TransaksiProdukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_keluar' => 15]);

		// Access the private static method using reflection
		$method = new ReflectionMethod(UnitProdukResource::class, 'calculateStokAkhir');
		$method->setAccessible(true);
		$stokAkhir = $method->invoke(null, $unitProduk);

		// stok_awal + stok_masuk - stok_keluar = 100 + 20 - 15 = 105
		expect($stokAkhir)->toBe(105);
	});
});

// Test the Filament Resource
describe('UnitProduk Filament Resource', function () {
	it('can render the list page', function () {
		livewire(UnitProdukResource\Pages\ListUnitProduks::class)
			->assertSuccessful();
	});

	it('can render the create page', function () {
		livewire(UnitProdukResource\Pages\CreateUnitProduk::class)
			->assertSuccessful();
	});

	it('can create a unit produk', function () {
		$newData = UnitProduk::factory()->make()->toArray();
		$newData['harga_modal'] = 100000; // Ensure numeric format for form submission

		livewire(UnitProdukResource\Pages\CreateUnitProduk::class)
			->fillForm($newData)
			->call('create')
			->assertHasNoFormErrors();

		expect(UnitProduk::where('sku', $newData['sku'])->exists())->toBeTrue();
	});

	it('requires form fields', function () {
		livewire(UnitProdukResource\Pages\CreateUnitProduk::class)
			->call('create')
			->assertHasFormErrors([
				'sku' => 'required',
				'nama_unit' => 'required',
				'harga_modal' => 'required',
				'stok_awal' => 'required',
			]);
	});

	it('can render the edit page and retrieve data', function () {
		$unitProduk = UnitProduk::factory()->create();

		livewire(UnitProdukResource\Pages\EditUnitProduk::class, ['record' => $unitProduk->getKey()])
			->assertSuccessful()
			->assertFormSet([
				'sku' => $unitProduk->sku,
				'nama_unit' => $unitProduk->nama_unit,
				'harga_modal' => $unitProduk->harga_modal,
				'stok_awal' => $unitProduk->stok_awal,
				'remarks' => $unitProduk->remarks,
			]);
	});

	it('can update a unit produk', function () {
		$unitProduk = UnitProduk::factory()->create();
		$updatedData = [
			'sku' => 'UPDATED-SKU',
			'nama_unit' => 'Updated Unit Name',
			'harga_modal' => 200000,
			'stok_awal' => 50,
			'remarks' => 'Updated remarks for testing.',
		];

		livewire(UnitProdukResource\Pages\EditUnitProduk::class, ['record' => $unitProduk->getKey()])
			->fillForm($updatedData)
			->call('save')
			->assertHasNoFormErrors();

		$unitProduk->refresh();
		expect($unitProduk->sku)->toBe('UPDATED-SKU');
		expect($unitProduk->nama_unit)->toBe('Updated Unit Name');
		expect($unitProduk->harga_modal)->toBe(200000);
		expect($unitProduk->stok_awal)->toBe(50);
		expect($unitProduk->remarks)->toBe('Updated remarks for testing.');
	});

	it('can delete a unit produk', function () {
		$unitProduk = UnitProduk::factory()->create();

		livewire(UnitProdukResource\Pages\ListUnitProduks::class)
			->callTableBulkAction('delete', [$unitProduk]);

		assertSoftDeleted($unitProduk);
	});

	it('can bulk delete unit produks', function () {
		$unitProduks = UnitProduk::factory()->count(3)->create();

		livewire(UnitProdukResource\Pages\ListUnitProduks::class)
			->callTableBulkAction('delete', $unitProduks);

		foreach ($unitProduks as $unitProduk) {
			assertSoftDeleted($unitProduk);
		}
	});

	it('displays correct table columns and calculations', function () {
		$unitProduk = UnitProduk::factory()->create(['stok_awal' => 100]);
		BarangMasukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_barang_masuk' => 20]);
		TransaksiProdukDetail::factory()->create(['unit_produk_id' => $unitProduk->id, 'jumlah_keluar' => 15]);

		livewire(UnitProdukResource\Pages\ListUnitProduks::class)
			->assertCanSeeTableRecords([$unitProduk])
			->assertSeeHtml($unitProduk->sku)
			->assertSeeHtml($unitProduk->nama_unit)
			->assertSeeHtml((string) $unitProduk->stok_awal)
			->assertSeeHtml('105') // stok_akhir: 100 + 20 - 15
			->assertSeeHtml('20')  // stok_masuk: 20
			->assertSeeHtml('15'); // stok_keluar: 15
	});
});