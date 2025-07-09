<?php

namespace App\Filament\Widgets;

use App\Models\Piutang;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PiutangList extends BaseWidget
{
	protected static ?int $sort = 3;
	protected static ?string $heading = 'Daftar Piutang Belum Lunas / Tercicil';

	public function getColumns(): int
	{
		return 2;
	}

	protected function getTableQuery(): Builder
	{
		return Piutang::query()
			->join('transaksi_produks', 'piutangs.transaksi_produk_id', '=', 'transaksi_produks.id')
			->whereIn('piutangs.status_pembayaran', ['belum lunas', 'tercicil'])
			->selectRaw("transaksi_produks.no_invoice as reference, piutangs.jatuh_tempo, piutangs.status_pembayaran as status, piutangs.id")
			->orderBy('piutangs.jatuh_tempo');
	}

	protected function getTableColumns(): array
	{
		return [
			TextColumn::make('reference')
				->label('No. Referensi')
				->sortable(),
			TextColumn::make('jatuh_tempo')
				->label('Jatuh Tempo')
				->date()
				->sortable(),
			BadgeColumn::make('status')
				->label('Status')
				->colors([
					'danger' => 'belum lunas',
					'warning' => 'tercicil',
					'success' => 'lunas',
				])
				->sortable(),
		];
	}
}