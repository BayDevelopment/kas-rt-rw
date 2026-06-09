<?php

namespace App\Filament\Resources\Wargas\Tables;

use App\Models\Warga;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class WargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // ── Nomor urut (tidak disimpan di DB, hanya tampilan) ──
                TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex()
                    ->width('50px'),

                // ── Nama Lengkap ──────────────────────────────────────
                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->tooltip('Nama sesuai KTP')
                    ->wrap(),

                // ── NIK ───────────────────────────────────────────────
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->copyable()                          // klik untuk salin NIK
                    ->copyMessage('NIK berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->fontFamily('mono')
                    ->tooltip('Klik untuk menyalin NIK')
                    ->placeholder('—'),

                // ── Nomor Rumah ───────────────────────────────────────
                TextColumn::make('no_rumah')
                    ->label('No. Rumah')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                // ── RT & RW (digabung agar hemat kolom) ──────────────
                TextColumn::make('rt')
                    ->label('RT / RW')
                    ->formatStateUsing(
                        fn($record) => 'RT ' . $record->rt . ' / RW ' . $record->rw
                    )
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Rukun Tetangga / Rukun Warga'),

                // ── Nomor HP ──────────────────────────────────────────
                TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor HP berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-phone')
                    ->tooltip('Klik untuk menyalin nomor HP'),

                // ── Status ────────────────────────────────────────────
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif'    => 'success',
                        'nonaktif' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default    => ucfirst($state),
                    })
                    ->tooltip(fn(string $state): string => match ($state) {
                        'aktif'    => 'Warga aktif & tercatat di wilayah ini',
                        'nonaktif' => 'Warga sudah pindah atau tidak aktif',
                        default    => '',
                    })
                    ->sortable(),

                // ── Tanggal Didaftarkan ───────────────────────────────
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)  // tersembunyi by default, bisa ditampilkan
                    ->tooltip('Tanggal warga pertama kali didaftarkan'),

                // ── Terakhir Diperbarui ───────────────────────────────
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()                              // "3 hari yang lalu", dsb.
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Waktu terakhir data warga diubah'),

            ])

            // ── FILTER ────────────────────────────────────────────────
            ->filters([

                SelectFilter::make('status')
                    ->label('Status Warga')
                    ->placeholder('Semua Status')
                    ->options([
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->indicator('Status'),

                SelectFilter::make('rt')
                    ->label('Rukun Tetangga (RT)')
                    ->placeholder('Semua RT')
                    ->options(
                        fn(): array => Warga::query()
                            ->whereNotNull('rt')
                            ->distinct()
                            ->orderBy('rt')
                            ->pluck('rt', 'rt')
                            ->mapWithKeys(fn($rt) => [
                                $rt => 'RT ' . str_pad((string) $rt, 3, '0', STR_PAD_LEFT),
                            ])
                            ->toArray()
                    )
                    ->indicator('RT'),

                SelectFilter::make('rw')
                    ->label('Rukun Warga (RW)')
                    ->placeholder('Semua RW')
                    ->options(
                        fn(): array => Warga::query()
                            ->whereNotNull('rw')
                            ->distinct()
                            ->orderBy('rw')
                            ->pluck('rw', 'rw')
                            ->mapWithKeys(fn($rw) => [
                                $rw => 'RW ' . str_pad((string) $rw, 3, '0', STR_PAD_LEFT),
                            ])
                            ->toArray()
                    )
                    ->indicator('RW'),

            ])

            // ── PENCARIAN GLOBAL ──────────────────────────────────────
            ->searchPlaceholder('Cari nama, NIK, atau nomor HP…')
            ->striped()                               // baris selang-seling agar mudah dibaca
            ->defaultSort('nama', 'asc')              // urut A–Z by default
            ->persistSortInSession()                  // ingat urutan saat navigasi
            ->persistSearchInSession()                // ingat kata kunci pencarian
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('Belum ada data warga')
            ->emptyStateDescription('Silakan tambahkan warga baru menggunakan tombol di atas.')

            // ── AKSI PER BARIS ────────────────────────────────────────
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

            // ── AKSI TOOLBAR (bulk) ───────────────────────────────────
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Warga')
                        ->modalDescription(
                            'Data warga yang dihapus tidak dapat dikembalikan. ' .
                                'Pastikan Anda sudah memilih warga yang benar.'
                        )
                        ->modalSubmitActionLabel('Ya, Hapus Sekarang'),
                ]),
            ]);
    }
}
