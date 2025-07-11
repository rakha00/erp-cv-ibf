<?php

use App\Filament\Resources\AsetResource;
use App\Models\Aset;
use Filament\Forms\Get;
use Filament\Forms\Set;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

describe('Aset Model', function () {
	it('has the correct fillable attributes', function () {
		$aset = new Aset();
		$fillable = ['nama_aset', 'harga', 'jumlah_aset'];
		expect($aset->getFillable())->toEqual($fillable);
	});
});

describe('AsetResource Form Calculations', function () {
	it('calculates total_harga_aset correctly', function () {
		$mockGet = Mockery::mock(Get::class);
		$mockSet = Mockery::mock(Set::class);

		$mockGet->shouldReceive('__invoke')
			->with('harga')
			->andReturn(1000000); // Direct integer
		$mockGet->shouldReceive('__invoke')
			->with('jumlah_aset')
			->andReturn(5); // Direct integer

		$mockSet->shouldReceive('__invoke')
			->with('total_harga_aset', '5,000,000') // Expected formatted output (1,000,000 * 5)
			->once();

		// Access the private static method using reflection
		$method = new ReflectionMethod(AsetResource::class, 'updateTotalHargaAset');
		$method->setAccessible(true);
		$method->invoke(null, $mockGet, $mockSet);
	});

	it('calculates total_harga_aset correctly with zero values', function () {
		$mockGet = Mockery::mock(Get::class);
		$mockSet = Mockery::mock(Set::class);

		$mockGet->shouldReceive('__invoke')
			->with('harga')
			->andReturn('Rp 0');
		$mockGet->shouldReceive('__invoke')
			->with('jumlah_aset')
			->andReturn('0');

		$mockSet->shouldReceive('__invoke')
			->with('total_harga_aset', '0')
			->once();

		$method = new ReflectionMethod(AsetResource::class, 'updateTotalHargaAset');
		$method->setAccessible(true);
		$method->invoke(null, $mockGet, $mockSet);
	});
});

describe('Aset Filament Resource', function () {
	it('can render the list page', function () {
		livewire(AsetResource\Pages\ListAsets::class)
			->assertSuccessful();
	});

	it('can render the create page', function () {
		livewire(AsetResource\Pages\CreateAset::class)
			->assertSuccessful();
	});

	it('can create an aset', function () {
		$newData = Aset::factory()->make()->toArray();
		$newData['harga'] = 1000000; // Ensure numeric format for form submission
		$newData['jumlah_aset'] = 2;

		livewire(AsetResource\Pages\CreateAset::class)
			->fillForm($newData)
			->call('create')
			->assertHasNoFormErrors();

		expect(Aset::where('nama_aset', $newData['nama_aset'])->exists())->toBeTrue();
	});

	it('requires form fields', function () {
		livewire(AsetResource\Pages\CreateAset::class)
			->call('create')
			->assertHasFormErrors([
				'nama_aset' => 'required',
				'harga' => 'required',
				'jumlah_aset' => 'required',
			]);
	});

	it('can render the edit page and retrieve data', function () {
		$aset = Aset::factory()->create();

		livewire(AsetResource\Pages\EditAset::class, ['record' => $aset->getKey()])
			->assertSuccessful()
			->assertFormSet([
				'nama_aset' => $aset->nama_aset,
				'harga' => $aset->harga,
				'jumlah_aset' => $aset->jumlah_aset,
			]);
	});

	it('can update an aset', function () {
		$aset = Aset::factory()->create();
		$updatedData = [
			'nama_aset' => 'Updated Aset Name',
			'harga' => 2000000,
			'jumlah_aset' => 3,
		];

		livewire(AsetResource\Pages\EditAset::class, ['record' => $aset->getKey()])
			->fillForm($updatedData)
			->call('save')
			->assertHasNoFormErrors();

		$aset->refresh();
		expect($aset->nama_aset)->toBe('Updated Aset Name');
		expect($aset->harga)->toBe(2000000);
		expect($aset->jumlah_aset)->toBe(3);
	});

	it('can delete an aset', function () {
		$aset = Aset::factory()->create();

		livewire(AsetResource\Pages\ListAsets::class)
			->callTableBulkAction('delete', [$aset]);

		expect(Aset::find($aset->id))->toBeNull();
	});

	it('can bulk delete asets', function () {
		$asets = Aset::factory()->count(3)->create();

		livewire(AsetResource\Pages\ListAsets::class)
			->callTableBulkAction('delete', $asets);

		foreach ($asets as $aset) {
			expect(Aset::find($aset->id))->toBeNull();
		}
	});

	it('displays correct table columns and calculations', function () {
		$aset = Aset::factory()->create(['harga' => 1000000, 'jumlah_aset' => 5]); // Total 5,000,000

		livewire(AsetResource\Pages\ListAsets::class)
			->assertCanSeeTableRecords([$aset])
			->assertSeeHtml($aset->nama_aset)
			->assertSeeHtml((string) $aset->jumlah_aset);
	});
});