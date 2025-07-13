<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KaryawanExport extends BaseExport implements FromCollection, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    protected \Illuminate\Database\Eloquent\Builder $query;

    protected bool $includeDetails;

    protected ?int $tahun;

    protected ?int $bulan;

    protected float $totalGajiPokok = 0;

    protected float $totalPenerimaan = 0;

    protected float $totalPotongan = 0;

    protected float $totalPendapatanBersih = 0;

    protected float $totalBonusTarget = 0;

    protected float $totalUangMakan = 0;

    protected float $totalTunjanganTransportasi = 0;

    protected float $totalThr = 0;

    protected float $totalKeterlambatan = 0;

    protected float $totalTanpaKeterangan = 0;

    protected float $totalPinjaman = 0;

    /**
     * Constructor for KaryawanExport.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  The Eloquent query builder for Karyawan model.
     * @param  string  $resourceTitle  The title for the exported resource.
     * @param  bool  $includeDetails  Whether to include detailed penghasilan information.
     * @param  int|null  $tahun  The year for filtering penghasilan details.
     * @param  int|null  $bulan  The month for filtering penghasilan details.
     */
    public function __construct(
        \Illuminate\Database\Eloquent\Builder $query,
        string $resourceTitle = 'Karyawan',
        bool $includeDetails = false,
        ?int $tahun = null,
        ?int $bulan = null
    ) {
        parent::__construct($resourceTitle);
        $this->query = $query;
        $this->includeDetails = $includeDetails;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    /**
     * Defines column formatting for the Excel sheet.
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'F' => '"Rp "#,##0',
            'G' => '"Rp "#,##0',
            'H' => '"Rp "#,##0',
            'I' => '"Rp "#,##0',
            'K' => '"Rp "#,##0',
            'L' => '"Rp "#,##0',
            'M' => '"Rp "#,##0',
            'N' => '"Rp "#,##0',
            'O' => '"Rp "#,##0',
            'P' => '"Rp "#,##0',
            'Q' => '"Rp "#,##0',
            'R' => '"Rp "#,##0',
        ];
    }

    /**
     * Retrieves the data collection for the export.
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $this->totalGajiPokok = 0;
        $this->totalPenerimaan = 0;
        $this->totalPotongan = 0;
        $this->totalPendapatanBersih = 0;
        $this->totalBonusTarget = 0;
        $this->totalUangMakan = 0;
        $this->totalTunjanganTransportasi = 0;
        $this->totalThr = 0;
        $this->totalKeterlambatan = 0;
        $this->totalTanpaKeterangan = 0;
        $this->totalPinjaman = 0;

        $karyawanQuery = $this->query->with([
            'penghasilanKaryawanDetails' => function ($query) {
                if ($this->tahun) {
                    $query->whereYear('tanggal', $this->tahun);
                }
                if ($this->bulan) {
                    $query->whereMonth('tanggal', $this->bulan);
                }
            },
        ]);

        $karyawans = $karyawanQuery->get();

        $processedData = $karyawans->map(function ($karyawan) {
            $penghasilan = $karyawan->penghasilanKaryawanDetails->first();

            $financials = $this->calculateKaryawanFinancials($karyawan, $penghasilan);

            $this->totalGajiPokok += $karyawan->gaji_pokok;
            $this->totalPenerimaan += $financials['total_penerimaan'];
            $this->totalPotongan += $financials['total_potongan'];
            $this->totalPendapatanBersih += $financials['pendapatan_bersih'];

            if ($this->includeDetails) {
                $this->totalBonusTarget += $penghasilan->bonus_target ?? 0;
                $this->totalUangMakan += $penghasilan->uang_makan ?? 0;
                $this->totalTunjanganTransportasi += $penghasilan->tunjangan_transportasi ?? 0;
                $this->totalThr += $penghasilan->thr ?? 0;
                $this->totalKeterlambatan += $penghasilan->keterlambatan ?? 0;
                $this->totalTanpaKeterangan += $penghasilan->tanpa_keterangan ?? 0;
                $this->totalPinjaman += $penghasilan->pinjaman ?? 0;
            }

            $rowData = [
                'karyawan_id' => $karyawan->id,
                'nik' => $karyawan->nik,
                'nama' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan,
                'status' => $karyawan->status,
                'no_hp' => $karyawan->no_hp,
                'gaji_pokok' => $karyawan->gaji_pokok,
                'total_penerimaan' => $financials['total_penerimaan'],
                'total_potongan' => $financials['total_potongan'],
                'pendapatan_bersih' => $financials['pendapatan_bersih'],
                'remarks_main' => $karyawan->remarks,
            ];

            if ($this->includeDetails) {
                $rowData = array_merge($rowData, [
                    'tanggal_detail' => $penghasilan ? Carbon::parse($penghasilan->tanggal)->format('Y-m-d') : null,
                    'bonus_target' => $penghasilan->bonus_target ?? null,
                    'uang_makan' => $penghasilan->uang_makan ?? null,
                    'tunjangan_transportasi' => $penghasilan->tunjangan_transportasi ?? null,
                    'thr' => $penghasilan->thr ?? null,
                    'keterlambatan' => $penghasilan->keterlambatan ?? null,
                    'tanpa_keterangan' => $penghasilan->tanpa_keterangan ?? null,
                    'pinjaman' => $penghasilan->pinjaman ?? null,
                    'remarks_detail' => $penghasilan->remarks ?? null,
                ]);
            }

            return (object) $rowData; // Cast to object for consistent access in map()
        });

        // Add summary row
        $processedData->push((object) [
            'nik' => 'TOTAL',
            'nama' => '',
            'jabatan' => '',
            'status' => '',
            'no_hp' => '',
            'gaji_pokok' => $this->totalGajiPokok,
            'total_penerimaan' => $this->totalPenerimaan,
            'total_potongan' => $this->totalPotongan,
            'pendapatan_bersih' => $this->totalPendapatanBersih,
            'remarks_main' => '',
            'tanggal_detail' => '', // Empty for summary row
            'bonus_target' => $this->totalBonusTarget,
            'uang_makan' => $this->totalUangMakan,
            'tunjangan_transportasi' => $this->totalTunjanganTransportasi,
            'thr' => $this->totalThr,
            'keterlambatan' => $this->totalKeterlambatan,
            'tanpa_keterangan' => $this->totalTanpaKeterangan,
            'pinjaman' => $this->totalPinjaman,
            'remarks_detail' => '',
            'is_summary_row' => true,
        ]);

        return $processedData;
    }

    /**
     * Calculates total penerimaan, total potongan, and net income for a Karyawan.
     *
     * @param  \App\Models\Karyawan  $karyawan  The Karyawan model instance.
     * @param  \App\Models\PenghasilanKaryawanDetail|null  $penghasilan  The PenghasilanKaryawanDetail instance, or null.
     * @return array Contains 'total_penerimaan', 'total_potongan', 'pendapatan_bersih'.
     */
    private function calculateKaryawanFinancials(\App\Models\Karyawan $karyawan, ?\App\Models\PenghasilanKaryawanDetail $penghasilan): array
    {
        $totalPenerimaan = 0;
        $totalPotongan = 0;
        $pendapatanBersih = $karyawan->gaji_pokok;

        if ($penghasilan) {
            $totalPenerimaan =
                ($penghasilan->bonus_target ?? 0) +
                ($penghasilan->uang_makan ?? 0) +
                ($penghasilan->tunjangan_transportasi ?? 0) +
                ($penghasilan->thr ?? 0);

            $totalPotongan =
                ($penghasilan->keterlambatan ?? 0) +
                ($penghasilan->tanpa_keterangan ?? 0) +
                ($penghasilan->pinjaman ?? 0);

            $pendapatanBersih = $karyawan->gaji_pokok + $totalPenerimaan - $totalPotongan;
        }

        return [
            'total_penerimaan' => $totalPenerimaan,
            'total_potongan' => $totalPotongan,
            'pendapatan_bersih' => $pendapatanBersih,
        ];
    }

    /**
     * Defines the headings for the Excel sheet.
     */
    public function headings(): array
    {
        if ($this->includeDetails) {
            return [
                'NIK',
                'Nama',
                'Jabatan',
                'Status',
                'No HP',
                'Gaji Pokok',
                'Total Penerimaan',
                'Total Potongan',
                'Pendapatan Bersih',
                'Remarks (Main)',
                'Tanggal Detail',
                'Bonus Target',
                'Uang Makan',
                'Tunjangan Transportasi',
                'THR',
                'Keterlambatan',
                'Tanpa Keterangan',
                'Pinjaman',
                'Remarks (Detail)',
            ];
        } else {
            return [
                'NIK',
                'Nama',
                'Jabatan',
                'Status',
                'No HP',
                'Gaji Pokok',
                'Total Penerimaan',
                'Total Potongan',
                'Pendapatan Bersih',
                'Remarks',
            ];
        }
    }

    /**
     * Maps a single row of data to the desired format for the Excel sheet.
     *
     * @param  mixed  $row  The data row.
     * @return array The formatted row.
     */
    public function map($row): array
    {
        // Handle summary row
        if (isset($row->is_summary_row) && $row->is_summary_row) {
            return [
                'TOTAL', // NIK
                '', // Nama
                '', // Jabatan
                '', // Status
                '', // No HP
                $row->gaji_pokok, // Gaji Pokok
                $row->total_penerimaan, // Total Penerimaan
                $row->total_potongan, // Total Potongan
                $row->pendapatan_bersih, // Pendapatan Bersih
                $this->includeDetails ? '' : ($row->remarks ?? ''), // Remarks (Main or empty)
                $this->includeDetails ? '' : '', // Tanggal Detail (empty)
                $this->includeDetails ? $row->bonus_target : '', // Bonus Target
                $this->includeDetails ? $row->uang_makan : '', // Uang Makan
                $this->includeDetails ? $row->tunjangan_transportasi : '', // Tunjangan Transportasi
                $this->includeDetails ? $row->thr : '', // THR
                $this->includeDetails ? $row->keterlambatan : '', // Keterlambatan
                $this->includeDetails ? $row->tanpa_keterangan : '', // Tanpa Keterangan
                $this->includeDetails ? $row->pinjaman : '', // Pinjaman
                $this->includeDetails ? '' : '', // Remarks (Detail) (empty)
            ];
        }

        // Regular data row
        if ($this->includeDetails) {
            return [
                $row->nik,
                $row->nama,
                $row->jabatan,
                $row->status,
                $row->no_hp,
                $row->gaji_pokok,
                $row->total_penerimaan,
                $row->total_potongan,
                $row->pendapatan_bersih,
                $row->remarks_main,
                $row->tanggal_detail,
                $row->bonus_target,
                $row->uang_makan,
                $row->tunjangan_transportasi,
                $row->thr,
                $row->keterlambatan,
                $row->tanpa_keterangan,
                $row->pinjaman,
                $row->remarks_detail,
            ];
        } else {
            return [
                $row->nik,
                $row->nama,
                $row->jabatan,
                $row->status,
                $row->no_hp,
                $row->gaji_pokok,
                $row->total_penerimaan,
                $row->total_potongan,
                $row->pendapatan_bersih,
                $row->remarks_main,
            ];
        }
    }
}
