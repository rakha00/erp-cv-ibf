<?php

use App\Filament\Resources\BarangMasukResource;
use App\Models\BarangMasuk;
use App\Models\PrincipleSubdealer;
use Carbon\Carbon;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

describe('BarangMasuk Model', function () {
	it('has the correct fillable attributes', function () {
		$barangMasuk = new BarangMasuk();
		$fillable = ['principle_subdealer_id', 'nomor_barang_masuk', 'tanggal', 'remarks'];
		expect($barangMasuk->getFillable())->toEqual($fillable);
	});

	it('uses SoftDeletes trait', function () {
		$barangMasuk = new BarangMasuk();
		expect(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses($barangMasuk)))->toBeTrue();
	});

	it('has principleSubdealer relationship', function () {
		$barangMasuk = BarangMasuk::factory()->create();
		expect($barangMasuk->principleSubdealer)->toBeInstanceOf(PrincipleSubdealer::class);
	});

	it('has barangMasukDetails relationship', function () {
		$barangMasuk = BarangMasuk::factory()->create();
		// Assuming BarangMasukDetailFactory exists and is correctly set up
		\App\Models\BarangMasukDetail::factory()->count(2)->create(['barang_masuk_id' => $barangMasuk->id]);

		expect($barangMasuk->barangMasukDetails)->toHaveCount(2);
		expect($barangMasuk->barangMasukDetails->first())->toBeInstanceOf(\App\Models\BarangMasukDetail::class);
	});
});

describe('BarangMasukResource Form Logic', function () {
	it('generates nomor_barang_masuk correctly for the first record of the day', function () {
		// Mock DB and Carbon for consistent testing
		DB::shouldReceive('transaction')
			->once()
			->andReturnUsing(function ($callback) {
				$callback();
			});

		// Ensure no existing records for the date
		BarangMasuk::query()->forceDelete();

		$mockSet = Mockery::mock(Set::class);
		$testDate = '2025-01-15';
		$expectedNomor = 'BM/15012025-1';

		$mockSet->shouldReceive('__invoke')
			->with('nomor_barang_masuk', $expectedNomor)
			->once();

		// Access the private static method using reflection
		$method = new ReflectionMethod(BarangMasukResource::class, 'generateNomorBarangMasuk');
		$method->setAccessible(true);
		$method->invoke(null, $testDate, $mockSet);
	});

	it('generates nomor_barang_masuk correctly for subsequent records of the day', function () {
		// Create an existing record for the same day
		BarangMasuk::factory()->create([
			'tanggal' => '2025-01-15',
			'nomor_barang_masuk' => 'BM/15012025-10',
		]);

		DB::shouldReceive('transaction')
			->once()
			->andReturnUsing(function ($callback) {
				$callback();
			});

		$mockSet = Mockery::mock(Set::class);
		$testDate = '2025-01-15';
		$expectedNomor = 'BM/15012025-11';

		$mockSet->shouldReceive('__invoke')
			->with('nomor_barang_masuk', $expectedNomor)
			->once();

		$method = new ReflectionMethod(BarangMasukResource::class, 'generateNomorBarangMasuk');
		$method->setAccessible(true);
		$method->invoke(null, $testDate, $mockSet);
	});

	it('calculates total_harga_modal correctly for a BarangMasuk record', function () {
		$barangMasuk = BarangMasuk::factory()->create();
		\App\Models\BarangMasukDetail::factory()->create([
			'barang_masuk_id' => $barangMasuk->id,
			'harga_modal' => 100000,
			'jumlah_barang_masuk' => 5,
		]);
		\App\Models\BarangMasukDetail::factory()->create([
			'barang_masuk_id' => $barangMasuk->id,
			'harga_modal' => 50000,
			'jumlah_barang_masuk' => 10,
		]);

		$method = new ReflectionMethod(BarangMasukResource::class, 'calculateTotalHargaModal');
		$method->setAccessible(true);
		$total = $method->invoke(null, $barangMasuk);

		expect($total)->toBe('1,000,000'); // 100k*5 + 50k*10 = 500k + 500k = 1M
	});
});

describe('BarangMasuk Filament Resource', function () {
	it('can render the list page', function () {
		livewire(BarangMasukResource\Pages\ListBarangMasuks::class)
			->assertSuccessful();
	});

	it('can render the create page', function () {
		livewire(BarangMasukResource\Pages\CreateBarangMasuk::class)
			->assertSuccessful();
	});

	it('can create a barang masuk record', function () {
		$principleSubdealer = PrincipleSubdealer::factory()->create();
		$testDate = Carbon::now()->format('Y-m-d');

		// Directly calculate the expectedNomor without relying on a helper function
		$latestRecord = BarangMasuk::whereDate('tanggal', $testDate)
			->withTrashed()
			->orderBy('created_at', 'desc')
			->first();

		$nextId = 1;
		if ($latestRecord) {
			$parts = explode('-', $latestRecord->nomor_barang_masuk);
			$lastId = end($parts);
			if (is_numeric($lastId)) {
				$nextId = (int) $lastId + 1;
			}
		}
		$expectedNomor = sprintf('BM/%s-%d', Carbon::parse($testDate)->format('dmY'), $nextId);

		$newData = [
			'principle_subdealer_id' => $principleSubdealer->id,
			'tanggal' => $testDate,
			'nomor_barang_masuk' => $expectedNomor, // This will be set by the resource
			'remarks' => 'Test remarks',
		];

		livewire(BarangMasukResource\Pages\CreateBarangMasuk::class)
			->fillForm($newData)
			->call('create')
			->assertHasNoFormErrors();

		expect(BarangMasuk::where('nomor_barang_masuk', $expectedNomor)->exists())->toBeTrue();
	});

	it('requires form fields', function () {
		livewire(BarangMasukResource\Pages\CreateBarangMasuk::class)
			->call('create')
			->assertHasFormErrors([
				'principle_subdealer_id' => 'required',
				'tanggal' => 'required',
				'nomor_barang_masuk' => 'required',
			]);
	});

	it('can render the edit page and retrieve data', function () {
		$barangMasuk = BarangMasuk::factory()->create();

		livewire(BarangMasukResource\Pages\EditBarangMasuk::class, ['record' => $barangMasuk->getKey()])
			->assertSuccessful()
			->assertFormSet([
				'principle_subdealer_id' => $barangMasuk->principle_subdealer_id,
				'tanggal' => $barangMasuk->tanggal->format('Y-m-d'),
				'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk,
				'remarks' => $barangMasuk->remarks,
			]);
	});

	it('can update a barang masuk record', function () {
		$barangMasuk = BarangMasuk::factory()->create();
		$newPrincipleSubdealer = PrincipleSubdealer::factory()->create();
		$updatedData = [
			'principle_subdealer_id' => $newPrincipleSubdealer->id,
			'tanggal' => Carbon::now()->subDays(5)->format('Y-m-d'),
			'nomor_barang_masuk' => $barangMasuk->nomor_barang_masuk, // Should not change if date is different
			'remarks' => 'Updated remarks for testing.',
		];

		livewire(BarangMasukResource\Pages\EditBarangMasuk::class, ['record' => $barangMasuk->getKey()])
			->fillForm($updatedData)
			->call('save')
			->assertHasNoFormErrors();

		$barangMasuk->refresh();
		expect($barangMasuk->principle_subdealer_id)->toBe($newPrincipleSubdealer->id);
		expect($barangMasuk->remarks)->toBe('Updated remarks for testing.');
	});

	it('can bulk delete barang masuk records', function () {
		$barangMasuks = BarangMasuk::factory()->count(3)->create();

		livewire(BarangMasukResource\Pages\ListBarangMasuks::class)
			->callTableBulkAction('delete', $barangMasuks);

		foreach ($barangMasuks as $barangMasuk) {
			assertSoftDeleted($barangMasuk);
		}
	});

	it('displays correct table columns and relationships', function () {
		$barangMasuk = BarangMasuk::factory()->create();

		livewire(BarangMasukResource\Pages\ListBarangMasuks::class)
			->assertCanSeeTableRecords([$barangMasuk])
			->assertSeeHtml($barangMasuk->nomor_barang_masuk)
			->assertSeeHtml($barangMasuk->principleSubdealer->nama);
	});
});