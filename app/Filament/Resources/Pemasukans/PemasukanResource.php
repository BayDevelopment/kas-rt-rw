<?php

namespace App\Filament\Resources\Pemasukans;

use App\Filament\Resources\Pemasukans\Pages\CreatePemasukan;
use App\Filament\Resources\Pemasukans\Pages\EditPemasukan;
use App\Filament\Resources\Pemasukans\Pages\ListPemasukans;
use App\Filament\Resources\Pemasukans\Schemas\PemasukanForm;
use App\Filament\Resources\Pemasukans\Tables\PemasukansTable;
use App\Models\Pemasukan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemasukanResource extends Resource
{
    protected static ?string $model = Pemasukan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

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
        return 'Pemasukan';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Pemasukan';
    }
    protected static ?string $navigationLabel = 'Pemasukan';
    protected static ?int    $navigationSort  = 1;
    // LAST ADD

    public static function form(Schema $schema): Schema
    {
        return PemasukanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemasukansTable::configure($table);
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
            'index' => ListPemasukans::route('/'),
            'create' => CreatePemasukan::route('/create'),
            'edit' => EditPemasukan::route('/{record}/edit'),
        ];
    }
}
