<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Alamat email berhasil disalin.'),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            'admin'        => 'Admin',
                            'pengurus_rw'  => 'Pengurus RW',
                            'bendahara_rt' => 'Bendahara RT',
                            'warga'        => 'Warga',
                            default        => 'Tidak Diketahui',
                        }
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            'admin'        => 'danger',
                            'pengurus_rw'  => 'warning',
                            'bendahara_rt' => 'info',
                            'warga'        => 'success',
                            default        => 'gray',
                        }
                    )
                    ->sortable(),
                TextColumn::make('rt')
                    ->label('RT')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('rw')
                    ->label('RW')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('no_hp')
                    ->label('Nomor Telepon')
                    ->placeholder('Belum diisi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin.'),

                TextColumn::make('warga.nama')
                    ->label('Data Warga')
                    ->placeholder('Belum terhubung')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('is_active')
                    ->label('Status Akun')
                    ->badge()
                    ->formatStateUsing(
                        fn($state): string => (bool) $state
                            ? 'Aktif'
                            : 'Nonaktif'
                    )
                    ->color(
                        fn($state): string => (bool) $state
                            ? 'success'
                            : 'danger'
                    )
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label('Status Email')
                    ->badge()
                    ->formatStateUsing(fn($state): string => filled($state) ? 'Terverifikasi' : 'Belum Verifikasi')
                    ->color(fn($state): string => filled($state) ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran Pengguna')
                    ->options([
                        'admin'        => 'Admin',
                        'pengurus_rw'  => 'Pengurus RW',
                        'bendahara_rt' => 'Bendahara RT',
                        'warga'        => 'Warga',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->placeholder('Semua status akun')
                    ->trueLabel('Hanya akun aktif')
                    ->falseLabel('Hanya akun nonaktif'),
                SelectFilter::make('rt')
                    ->label('RT')
                    ->options(
                        fn(): array => User::query()
                            ->whereNotNull('rt')
                            ->where('rt', '!=', '')
                            ->orderBy('rt')
                            ->pluck('rt', 'rt')
                            ->all()
                    )
                    ->searchable(),

                SelectFilter::make('rw')
                    ->label('RW')
                    ->options(
                        fn(): array => User::query()
                            ->whereNotNull('rw')
                            ->where('rw', '!=', '')
                            ->orderBy('rw')
                            ->pluck('rw', 'rw')
                            ->all()
                    )
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('verify_email')
                        ->label('Verifikasi Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Verifikasi email pengguna?')
                        ->modalDescription('Email pengguna akan ditandai sebagai sudah terverifikasi.')
                        ->modalSubmitActionLabel('Ya, verifikasi')
                        ->visible(fn(User $record): bool => blank($record->email_verified_at))
                        ->action(function (User $record): void {
                            $record->forceFill([
                                'email_verified_at' => Carbon::now(),
                            ])->save();

                            Notification::make()
                                ->success()
                                ->title('Email berhasil diverifikasi')
                                ->body('Alamat email pengguna sudah ditandai sebagai terverifikasi.')
                                ->send();
                        }),

                    Action::make('unverify_email')
                        ->label('Batalkan Verifikasi')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan verifikasi email?')
                        ->modalDescription('Email pengguna akan kembali ditandai sebagai belum terverifikasi.')
                        ->modalSubmitActionLabel('Ya, batalkan')
                        ->visible(fn(User $record): bool => filled($record->email_verified_at))
                        ->action(function (User $record): void {
                            $record->forceFill([
                                'email_verified_at' => null,
                            ])->save();

                            Notification::make()
                                ->warning()
                                ->title('Verifikasi email dibatalkan')
                                ->body('Alamat email pengguna sekarang berstatus belum terverifikasi.')
                                ->send();
                        }),
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
