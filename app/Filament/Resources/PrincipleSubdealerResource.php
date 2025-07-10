<?php

namespace App\Filament\Resources;

use App\Exports\PrincipleSubdealerExport;
use App\Filament\Resources\PrincipleSubdealerResource\Pages;
use App\Models\PrincipleSubdealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class PrincipleSubdealerResource extends Resource
{
    protected static ?string $model = PrincipleSubdealer::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'Principle/Subdealer';

    protected static ?string $pluralModelLabel = 'Principle/Subdealer';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sales')
                    ->label('Sales')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No HP')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('remarks')
                    ->label('Remarks')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sales')
                    ->label('Sales')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Table $table) => self::exportPrincipleSubdealerExcel($table)),
            ]);
    }

    private static function exportPrincipleSubdealerExcel(Table $table): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $livewire = $table->getLivewire();
        $query = $livewire->getFilteredTableQuery();
        $resourceTitle = static::$pluralModelLabel;

        return \Maatwebsite\Excel\Facades\Excel::download(new PrincipleSubdealerExport($query, $resourceTitle), 'principle_subdealer.xlsx');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrincipleSubdealers::route('/'),
            'create' => Pages\CreatePrincipleSubdealer::route('/create'),
            'edit' => Pages\EditPrincipleSubdealer::route('/{record}/edit'),
        ];
    }
}
