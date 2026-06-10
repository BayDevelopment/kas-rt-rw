<?php

namespace App\Filament\Resources\Pengeluarans\Tables;

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
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportHtmlAttributeForwarding\SupportHtmlAttributeForwarding;

class PengeluaransTable
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

                TextColumn::make('periode_id')
                    ->label('Periode')
                    ->formatStateUsing(
                        fn($record): string => ($record->periode?->bulan ?? '-') . ' ' . ($record->periode?->tahun ?? '-')
                    )
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-calendar-days')
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'operasional' => 'Operasional',
                        'sosial' => 'Sosial',
                        'pembangunan' => 'Pembangunan',
                        'lainnya' => 'Lainnya',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'operasional' => 'warning',
                        'sosial' => 'success',
                        'pembangunan' => 'info',
                        'lainnya' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'operasional' => 'heroicon-o-wrench-screwdriver',
                        'sosial' => 'heroicon-o-heart',
                        'pembangunan' => 'heroicon-o-building-office-2',
                        'lainnya' => 'heroicon-o-ellipsis-horizontal-circle',
                        default => 'heroicon-o-tag',
                    })
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Nominal')
                    ->money('IDR')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->weight('bold')
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar')
                    ->sortable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(45)
                    ->tooltip(fn($state): ?string => $state)
                    ->wrap()
                    ->searchable(),

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
                    ->preload()
                    ->visible(fn() => Auth::user()?->isAdmin()),

                SelectFilter::make('periode_id')
                    ->label('Periode')
                    ->options(
                        \App\Models\Periode::query()
                            ->orderByDesc('tahun')
                            ->get()
                            ->mapWithKeys(fn($periode) => [
                                $periode->id => "{$periode->bulan} {$periode->tahun}",
                            ])
                            ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'operasional' => 'Operasional',
                        'sosial' => 'Sosial',
                        'pembangunan' => 'Pembangunan',
                        'lainnya' => 'Lainnya',
                    ])
                    ->native(false),

                Filter::make('tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),

                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn(Builder $query, $date) => $query->whereDate('tanggal', '>=', $date)
                            )
                            ->when(
                                $data['sampai'] ?? null,
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
