<?php

namespace App\Filament\Widgets;

use App\Models\BarangMasukDetail;
use App\Models\PenghasilanKaryawanDetail;
use App\Models\TransaksiProdukDetail;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;

class TotalKeuntunganKantorTotalGajiKaryawanOverview extends Widget implements HasForms
{
	use InteractsWithForms;

	protected static string $view = 'filament.widgets.total-keuntungan-kantor-total-gaji-karyawan-overview';

	protected static ?int $sort = 1;

	protected int|string|array $columnSpan = 'full';

	public ?int $year;
	public ?int $month;

	public function mount(): void
	{
		$this->year = (int) Carbon::now()->year;
		$this->month = (int) Carbon::now()->month;
	}

	public function form(Form $form): Form
	{
		return $form
			->schema([
				Select::make('year')
					->options(function () {
						$currentYear = Carbon::now()->year;
						$years = range($currentYear, $currentYear + 5);
						return array_combine($years, $years);
					})
					->live()
					->afterStateUpdated(function ($state) {
						$this->year = $state;
					}),
				Select::make('month')
					->options([
						1 => 'Januari',
						2 => 'Februari',
						3 => 'Maret',
						4 => 'April',
						5 => 'Mei',
						6 => 'Juni',
						7 => 'Juli',
						8 => 'Agustus',
						9 => 'September',
						10 => 'Oktober',
						11 => 'November',
						12 => 'Desember',
					])
					->live()
					->afterStateUpdated(function ($state) {
						$this->month = $state;
					}),
			]);
	}

	public function render(): View
	{
		$stats = $this->getStatsOverview();

		return view(static::$view, compact('stats'));
	}

	protected function getStatsOverview(): array
	{
		$year = $this->year ?? Carbon::now()->year;
		$month = $this->month ?? Carbon::now()->month;

		// Calculate Total Gaji Karyawan (sebelum dikurang kasbon)
		$totalGajiKaryawan = PenghasilanKaryawanDetail::whereYear('tanggal', $year)
			->whereMonth('tanggal', $month)
			->sum('bonus_target') +
			PenghasilanKaryawanDetail::whereYear('tanggal', $year)
				->whereMonth('tanggal', $month)
				->sum('uang_makan') +
			PenghasilanKaryawanDetail::whereYear('tanggal', $year)
				->whereMonth('tanggal', $month)
				->sum('tunjangan_transportasi') +
			PenghasilanKaryawanDetail::whereYear('tanggal', $year)
				->whereMonth('tanggal', $month)
				->sum('thr');

		// Calculate Total Income from product sales
		$totalIncome = TransaksiProdukDetail::whereHas('transaksiProduk', function ($query) use ($year, $month) {
			$query->whereYear('tanggal', $year)
				->whereMonth('tanggal', $month);
		})->sum('total_keuntungan');

		// Calculate Total Expenses from incoming goods
		$totalExpenses = BarangMasukDetail::whereHas('barangMasuk', function ($query) use ($year, $month) {
			$query->whereYear('tanggal', $year)
				->whereMonth('tanggal', $month);
		})->sum('total_harga_modal');

		// Calculate Total Keuntungan Kantor (sudah dikurang total gaji karyawan)
		$totalKeuntunganKantor = $totalIncome - $totalExpenses - $totalGajiKaryawan;

		return [
			Stat::make('Total Keuntungan Kantor', 'Rp ' . number_format($totalKeuntunganKantor, 0, ',', '.'))
				->icon('heroicon-o-currency-dollar')
				->color($totalKeuntunganKantor >= 0 ? 'success' : 'danger'),
			Stat::make('Total Gaji Karyawan', 'Rp ' . number_format($totalGajiKaryawan, 0, ',', '.'))
				->icon('heroicon-o-users')
				->color('info'),
		];
	}

}