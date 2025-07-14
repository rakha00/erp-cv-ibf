<?php

namespace App\Filament\Widgets;

use App\Models\BarangMasukDetail;
use App\Models\Karyawan;
use App\Models\PenghasilanKaryawanDetail;
use App\Models\TransaksiProdukDetail;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialOverviewWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.financial-overview-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public ?int $year;

    public ?int $month;

    public ?string $salaryOverviewType = 'net_income'; // Default to net income

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
                Select::make('salaryOverviewType')
                    ->label('Tipe Gaji Karyawan')
                    ->options([
                        'basic_salary' => 'Total Gaji Pokok',
                        'total_income' => 'Total Gaji + Penerimaan',
                        'total_deductions' => 'Total Potongan',
                        'net_income' => 'Total Pendapatan Bersih',
                    ])
                    ->default('net_income')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->salaryOverviewType = $state;
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

        $totalGajiKaryawan = 0;
        $totalPenerimaan = 0;
        $totalPotongan = 0;
        $totalGajiPokok = Karyawan::sum('gaji_pokok');

        $penghasilanKaryawanDetails = PenghasilanKaryawanDetail::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();

        foreach ($penghasilanKaryawanDetails as $detail) {
            $totalPenerimaan += $detail->bonus_target + $detail->uang_makan + $detail->tunjangan_transportasi + $detail->thr;
            $totalPotongan += $detail->keterlambatan + $detail->tanpa_keterangan + $detail->pinjaman;
        }

        switch ($this->salaryOverviewType) {
            case 'basic_salary':
                $totalGajiKaryawan = $totalGajiPokok;
                break;
            case 'total_income':
                $totalGajiKaryawan = $totalGajiPokok + $totalPenerimaan;
                break;
            case 'total_deductions':
                $totalGajiKaryawan = $totalPotongan;
                break;
            case 'net_income':
                $totalGajiKaryawan = ($totalGajiPokok + $totalPenerimaan) - $totalPotongan;
                break;
            default:
                $totalGajiKaryawan = ($totalGajiPokok + $totalPenerimaan) - $totalPotongan; // Default to net income
                break;
        }

        // Calculate Total Income from product sales
        $totalIncome = TransaksiProdukDetail::whereHas('transaksiProduk', function ($query) use ($year, $month) {
            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        })->sum('total_keuntungan');

        // Calculate Total Keuntungan Kantor (sudah dikurang total gaji karyawan)
        $totalKeuntunganKantor = $totalIncome - $totalGajiKaryawan;

        // Calculate Total Transaksi Produk (with year and month filter)
        $totalTransaksiProduk = TransaksiProdukDetail::whereHas('transaksiProduk', function ($query) use ($year, $month) {
            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        })->sum(DB::raw('harga_jual * jumlah_keluar'));

        // Calculate Total Barang Masuk (with year and month filter)
        $totalBarangMasuk = BarangMasukDetail::whereHas('barangMasuk', function ($query) use ($year, $month) {
            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        })->sum(DB::raw('harga_modal * jumlah_barang_masuk'));

        return [
            Stat::make('Total Keuntungan Kantor', 'Rp '.number_format($totalKeuntunganKantor, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color($totalKeuntunganKantor >= 0 ? 'success' : 'danger'),
            Stat::make($this->getSalaryLabel(), 'Rp '.number_format($totalGajiKaryawan, 0, ',', '.'))
                ->icon('heroicon-o-users')
                ->color('info'),
            Stat::make('Total Keuntungan Produk', 'Rp '.number_format($totalIncome, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Total Barang Masuk', 'Rp '.number_format($totalBarangMasuk, 0, ',', '.'))
                ->icon('heroicon-o-archive-box')
                ->color('info'),
        ];
    }

    protected function getSalaryLabel(): string
    {
        return match ($this->salaryOverviewType) {
            'basic_salary' => 'Total Gaji Pokok Karyawan',
            'total_income' => 'Total Gaji + Penerimaan Karyawan',
            'total_deductions' => 'Total Potongan Karyawan',
            'net_income' => 'Total Pendapatan Bersih Karyawan',
            default => 'Total Pendapatan Bersih Karyawan', // Default label
        };
    }
}
