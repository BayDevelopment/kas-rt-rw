<?php

namespace App\Filament\Resources\Pemasukans\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PemasukansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.nama')
                    ->label('Wilayah')
                    ->icon('heroicon-o-map-pin')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('warga.nama')
                    ->label('Warga')
                    ->description(fn($record): string => 'No. Rumah: ' . ($record->warga?->no_rumah ?? '-'))
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('periode_id')
                    ->label('Periode')
                    ->formatStateUsing(
                        fn($record): string => ($record->periode?->bulan ?? '-') . ' ' . ($record->periode?->tahun ?? '-')
                    )
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-calendar-days')
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Nominal')
                    ->money('IDR')
                    ->icon('heroicon-o-banknotes')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada keterangan')
                    ->limit(35)
                    ->tooltip(fn($state): ?string => $state)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->since()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tenant_id')
                    ->label('Wilayah RT/RW')
                    ->relationship('tenant', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('periode_id')
                    ->label('Periode')
                    ->options(
                        \App\Models\Periode::query()
                            ->orderByDesc('tahun')
                            ->get()
                            ->mapWithKeys(fn($periode) => [
                                $periode->id => "{$periode->bulan} {$periode->tahun}"
                            ])
                            ->toArray()
                    ),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '<=', $date)
                            );
                    }),
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
