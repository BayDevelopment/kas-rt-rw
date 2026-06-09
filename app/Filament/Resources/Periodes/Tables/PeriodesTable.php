<?php

namespace App\Filament\Resources\Periodes\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PeriodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->state(fn($record) => $record->bulan . ' ' . $record->tahun)
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('target_kas')
                    ->label('Target Kas')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'tutup' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([

                SelectFilter::make('status')
                    ->label('Status Periode')
                    ->options([
                        'aktif' => 'Aktif',
                        'tutup' => 'Tutup',
                    ]),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(
                        fn() => \App\Models\Periode::query()
                            ->distinct()
                            ->orderByDesc('tahun')
                            ->pluck('tahun', 'tahun')
                            ->toArray()
                    ),

                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options(
                        fn() => \App\Models\Periode::query()
                            ->distinct()
                            ->pluck('bulan', 'bulan')
                            ->toArray()
                    ),

            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data?')
                        ->modalDescription('Data akan dipindahkan ke trash.')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Data berhasil dihapus')
                                ->success()
                        ),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
