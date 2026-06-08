<?php

namespace App\Filament\Resources\Pengeluarans;

use App\Filament\Resources\Pengeluarans\Pages\CreatePengeluaran;
use App\Filament\Resources\Pengeluarans\Pages\EditPengeluaran;
use App\Filament\Resources\Pengeluarans\Pages\ListPengeluarans;
use App\Filament\Resources\Pengeluarans\Schemas\PengeluaranForm;
use App\Filament\Resources\Pengeluarans\Tables\PengeluaransTable;
use App\Models\Pengeluaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingDown;

    protected static ?string $recordTitleAttribute = 'warga_id';

     // NEW ADD
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Transaksi';
    }
    public static function getNavigationSort(): ?int
    {
        return 1; // ganti angka sesuai urutan yang lo mau
    }
    public static function getModelLabel(): string
    {
        return 'Pengeluaran';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Pengeluaran';
    }
    protected static ?string $navigationLabel = 'Pengeluaran';
    protected static ?int    $navigationSort  = 2;
    // LAST ADD

    public static function form(Schema $schema): Schema
    {
        return PengeluaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengeluaransTable::configure($table);
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
            'index' => ListPengeluarans::route('/'),
            'create' => CreatePengeluaran::route('/create'),
            'edit' => EditPengeluaran::route('/{record}/edit'),
        ];
    }
}
