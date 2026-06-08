<?php

namespace App\Filament\Resources\Wargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                        fn ($record) => 'RT ' . $record->rt . ' / RW ' . $record->rw
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
                    ->color(fn (string $state): string => match ($state) {
                        'aktif'    => 'success',
                        'nonaktif' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif'    => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        default    => ucfirst($state),
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
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
                    ->indicator('Status'),           // tampil sebagai chip di atas tabel

                SelectFilter::make('rt')
                    ->label('Rukun Tetangga (RT)')
                    ->placeholder('Semua RT')
                    ->options(
                        // opsi RT 001–020; sesuaikan sesuai kebutuhan wilayah
                        collect(range(1, 20))
                            ->mapWithKeys(fn ($n) => [
                                str_pad($n, 3, '0', STR_PAD_LEFT) =>
                                'RT ' . str_pad($n, 3, '0', STR_PAD_LEFT),
                            ])
                            ->all()
                    )
                    ->indicator('RT'),

                SelectFilter::make('rw')
                    ->label('Rukun Warga (RW)')
                    ->placeholder('Semua RW')
                    ->options(
                        collect(range(1, 10))
                            ->mapWithKeys(fn ($n) => [
                                str_pad($n, 3, '0', STR_PAD_LEFT) =>
                                'RW ' . str_pad($n, 3, '0', STR_PAD_LEFT),
                            ])
                            ->all()
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
                ViewAction::make()
                    ->label('Lihat')
                    ->tooltip('Lihat detail data warga'),

                EditAction::make()
                    ->label('Edit')
                    ->tooltip('Ubah data warga'),
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