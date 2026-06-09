<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Models\Tenants;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nama')
                    ->label('Nama Wilayah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->alamat),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('kelurahan')
                    ->label('Kelurahan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('kota')
                    ->label('Kota/Kabupaten')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('wargas_count')
                    ->label('Jumlah Warga')
                    ->counts('wargas')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                SelectFilter::make('is_active')
                    ->label('Status Wilayah')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Nonaktif',
                    ]),

                SelectFilter::make('provinsi')
                    ->label('Provinsi')
                    ->options(
                        fn(): array => Tenants::query()
                            ->whereNotNull('provinsi')
                            ->distinct()
                            ->orderBy('provinsi')
                            ->pluck('provinsi', 'provinsi')
                            ->toArray()
                    ),

                SelectFilter::make('kota')
                    ->label('Kota/Kabupaten')
                    ->options(
                        fn(): array => Tenants::query()
                            ->whereNotNull('kota')
                            ->distinct()
                            ->orderBy('kota')
                            ->pluck('kota', 'kota')
                            ->toArray()
                    ),

                SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->options(
                        fn(): array => Tenants::query()
                            ->whereNotNull('kecamatan')
                            ->distinct()
                            ->orderBy('kecamatan')
                            ->pluck('kecamatan', 'kecamatan')
                            ->toArray()
                    ),

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat')
                        ->icon('heroicon-o-eye')
                        ->tooltip('Lihat detail data warga'),

                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->tooltip('Ubah data warga'),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data warga?')
                        ->modalDescription('Data warga akan dihapus permanen dari sistem dan tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->modalCancelActionLabel('Batal')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Data warga berhasil dihapus permanen.')
                                ->success()
                        ),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
