<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class KaryawanExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
	protected $query;
	protected $includeDetails;
	protected $tahun;
	protected $bulan;
	protected $lastKaryawanId = null; // To track the last Karyawan ID for de-duplication

	public function __construct($query, $resourceTitle = 'Karyawan', $includeDetails = false, $tahun = null, $bulan = null)
	{
		parent::__construct($resourceTitle);
		$this->query = $query;
		$this->includeDetails = $includeDetails;
		$this->tahun = $tahun;
		$this->bulan = $bulan;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function collection()
	{
		if ($this->includeDetails) {
			$karyawans = $this->query->with([
				'penghasilanKaryawanDetails' => function ($query) {
					if ($this->tahun) {
						$query->whereYear('tanggal', $this->tahun);
					}
					if ($this->bulan) {
						$query->whereMonth('tanggal', $this->bulan);
					}
				}
			])->get();

			$flattenedData = collect();

			foreach ($karyawans as $karyawan) {
				$penghasilan = $karyawan->penghasilanKaryawanDetails->first();

				$totalPenerimaan = $karyawan->gaji_pokok +
					($penghasilan->bonus_target ?? 0) +
					($penghasilan->uang_makan ?? 0) +
					($penghasilan->tunjangan_transportasi ?? 0) +
					($penghasilan->thr ?? 0);

				$totalPotongan = ($penghasilan->keterlambatan ?? 0) +
					($penghasilan->tanpa_keterangan ?? 0) +
					($penghasilan->pinjaman ?? 0);

				$pendapatanBersih = $totalPenerimaan - $totalPotongan;

				$flattenedData->push([
					'karyawan_id' => $karyawan->id,
					'nik' => $karyawan->nik,
					'nama' => $karyawan->nama,
					'jabatan' => $karyawan->jabatan,
					'status' => $karyawan->status,
					'no_hp' => $karyawan->no_hp,
					'gaji_pokok' => $karyawan->gaji_pokok,
					'total_penerimaan' => $totalPenerimaan,
					'total_potongan' => $totalPotongan,
					'pendapatan_bersih' => $pendapatanBersih,
					'remarks_main' => $karyawan->remarks,
					// Details from PenghasilanKaryawanDetail
					'detail_id' => $penghasilan->id ?? null,
					'tanggal_detail' => $penghasilan ? Carbon::parse($penghasilan->tanggal)->format('Y-m-d') : null,
					'bonus_target' => $penghasilan->bonus_target ?? null,
					'uang_makan' => $penghasilan->uang_makan ?? null,
					'tunjangan_transportasi' => $penghasilan->tunjangan_transportasi ?? null,
					'thr' => $penghasilan->thr ?? null,
					'keterlambatan' => $penghasilan->keterlambatan ?? null,
					'tanpa_keterangan' => $penghasilan->tanpa_keterangan ?? null,
					'pinjaman' => $penghasilan->pinjaman ?? null,
				]);
			}
			return $flattenedData;
		} else {
			return $this->query->get();
		}
	}

	public function headings(): array
	{
		if ($this->includeDetails) {
			return [
				'ID Karyawan',
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
				'ID Detail',
				'Tanggal Detail',
				'Bonus Target',
				'Uang Makan',
				'Tunjangan Transportasi',
				'THR',
				'Keterlambatan',
				'Tanpa Keterangan',
				'Pinjaman',
			];
		} else {
			return [
				'ID',
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

	public function map($row): array
	{
		if ($this->includeDetails) {
			$currentKaryawanId = $row['karyawan_id'];
			$isNewParent = ($this->lastKaryawanId !== $currentKaryawanId);
			$this->lastKaryawanId = $currentKaryawanId;

			return [
				$isNewParent ? $row['karyawan_id'] : '',
				$isNewParent ? $row['nik'] : '',
				$isNewParent ? $row['nama'] : '',
				$isNewParent ? $row['jabatan'] : '',
				$isNewParent ? $row['status'] : '',
				$isNewParent ? $row['no_hp'] : '',
				$isNewParent ? $row['gaji_pokok'] : '',
				$isNewParent ? $row['total_penerimaan'] : '',
				$isNewParent ? $row['total_potongan'] : '',
				$isNewParent ? $row['pendapatan_bersih'] : '',
				$isNewParent ? $row['remarks_main'] : '',
				$row['detail_id'],
				$row['tanggal_detail'],
				$row['bonus_target'],
				$row['uang_makan'],
				$row['tunjangan_transportasi'],
				$row['thr'],
				$row['keterlambatan'],
				$row['tanpa_keterangan'],
				$row['pinjaman'],
			];
		} else {
			$penghasilan = $row->penghasilanKaryawanDetails->first();

			$totalPenerimaan = $row->gaji_pokok +
				($penghasilan->bonus_target ?? 0) +
				($penghasilan->uang_makan ?? 0) +
				($penghasilan->tunjangan_transportasi ?? 0) +
				($penghasilan->thr ?? 0);

			$totalPotongan = ($penghasilan->keterlambatan ?? 0) +
				($penghasilan->tanpa_keterangan ?? 0) +
				($penghasilan->pinjaman ?? 0);

			$pendapatanBersih = $totalPenerimaan - $totalPotongan;

			return [
				$row->id,
				$row->nik,
				$row->nama,
				$row->jabatan,
				$row->status,
				$row->no_hp,
				$row->gaji_pokok,
				$totalPenerimaan,
				$totalPotongan,
				$pendapatanBersih,
				$row->remarks,
			];
		}
	}
}