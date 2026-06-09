<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Auth\Events\Registered;
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

                TextColumn::make('tenant.nama')
                    ->label('Tenant')
                    ->placeholder('Belum ada tenant')
                    ->searchable()
                    ->sortable(),

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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin'        => 'Admin',
                        'pengurus_rw'  => 'Pengurus RW',
                        'pengurus_rt'  => 'Pengurus RT',
                        'bendahara_rw' => 'Bendahara RW',
                        'bendahara_rt' => 'Bendahara RT',
                        'warga'        => 'Warga',
                        default        => 'Tidak Diketahui',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'admin'        => 'danger',
                        'pengurus_rw'  => 'warning',
                        'pengurus_rt'  => 'warning',
                        'bendahara_rw' => 'info',
                        'bendahara_rt' => 'info',
                        'warga'        => 'success',
                        default        => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('warga.nama')
                    ->label('Data Warga')
                    ->placeholder('Belum terhubung')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('is_active')
                    ->label('Status Akun')
                    ->badge()
                    ->formatStateUsing(fn($state): string => (bool) $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn($state): string => (bool) $state ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label('Status Email')
                    ->badge()
                    ->getStateUsing(fn(User $record): string => $record->email_verified_at
                        ? 'Terverifikasi'
                        : 'Belum Terverifikasi')
                    ->color(fn(User $record): string => $record->email_verified_at
                        ? 'success'
                        : 'danger')
                    ->sortable(),
            ])

            ->filters([

                SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('role')
                    ->label('Peran Pengguna')
                    ->options([
                        'admin'        => 'Admin',
                        'pengurus_rw'  => 'Pengurus RW',
                        'pengurus_rt'  => 'Pengurus RT',
                        'bendahara_rw' => 'Bendahara RW',
                        'bendahara_rt' => 'Bendahara RT',
                        'warga'        => 'Warga',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->placeholder('Semua status akun')
                    ->trueLabel('Hanya akun aktif')
                    ->falseLabel('Hanya akun nonaktif'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('send_verification_email')
                        ->label('Kirim Verifikasi')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn(User $record): bool => ! $record->hasVerifiedEmail())
                        ->action(function (User $record): void {

                            $record->sendEmailVerificationNotification();

                            Notification::make()
                                ->success()
                                ->title('Email verifikasi berhasil dikirim')
                                ->body("Email verifikasi telah dikirim ke {$record->email}")
                                ->send();
                        }),

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
                        ->modalDescription('Data akan dipindahkan ke trash.'),
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
