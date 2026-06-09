<?php

namespace App\Filament\Resources\Wargas\Pages;

use App\Filament\Resources\Wargas\WargaResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewWarga extends ViewRecord
{
    protected static string $resource = WargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pribadi')
                    ->description('Informasi utama warga berdasarkan data KTP.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('nik')
                            ->label('NIK')
                            ->badge()
                            ->color('gray')
                            ->copyable()
                            ->icon('heroicon-o-identification'),

                        TextEntry::make('no_hp')
                            ->label('Nomor HP / WhatsApp')
                            ->copyable()
                            ->placeholder('-')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('no_rumah')
                            ->label('Nomor Rumah')
                            ->placeholder('-')
                            ->icon('heroicon-o-home'),
                    ]),

                Section::make('Alamat RT/RW')
                    ->description('Informasi wilayah tempat tinggal warga.')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('rt')
                            ->label('RT')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => 'RT ' . str_pad((string) $state, 3, '0', STR_PAD_LEFT)),

                        TextEntry::make('rw')
                            ->label('RW')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn ($state): string => 'RW ' . str_pad((string) $state, 3, '0', STR_PAD_LEFT)),
                    ]),

                Section::make('Status Keanggotaan')
                    ->description('Jabatan dan status warga dalam sistem RT/RW.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('jabatan')
                            ->label('Jabatan')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'warga' => 'Warga',
                                'ketua_rt' => 'Ketua RT',
                                'sekretaris_rt' => 'Sekretaris RT',
                                'bendahara_rt' => 'Bendahara RT',
                                'ketua_rw' => 'Ketua RW',
                                'sekretaris_rw' => 'Sekretaris RW',
                                'bendahara_rw' => 'Bendahara RW',
                                default => '-',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'ketua_rt', 'ketua_rw' => 'success',
                                'sekretaris_rt', 'sekretaris_rw' => 'warning',
                                'bendahara_rt', 'bendahara_rw' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                default => '-',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'aktif' => 'success',
                                'nonaktif' => 'danger',
                                default => 'gray',
                            }),
                    ]),

                Section::make('Informasi Sistem')
                    ->description('Informasi pencatatan data.')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
