<?php

namespace App\Filament\Resources\Periodes\Schemas;

use App\Models\Tenants;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PeriodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Periode Iuran')
                    ->description('Kelola periode iuran kas berdasarkan bulan dan tahun.')
                    ->icon('heroicon-o-calendar-days')
                    ->columnSpanFull()
                    ->schema([

                        Select::make('tenant_id')
                            ->label('Wilayah RT/RW')
                            ->relationship('tenant', 'nama')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->visible(fn() => Auth::user()?->isAdmin())
                            ->validationMessages([
                                'required' => 'Wilayah RT/RW wajib dipilih.',
                            ]),

                        Select::make('bulan')
                            ->label('Bulan')
                            ->placeholder('Pilih bulan periode')
                            ->helperText('Pilih bulan periode iuran kas.')
                            ->options([
                                'Januari'   => 'Januari',
                                'Februari'  => 'Februari',
                                'Maret'     => 'Maret',
                                'April'     => 'April',
                                'Mei'       => 'Mei',
                                'Juni'      => 'Juni',
                                'Juli'      => 'Juli',
                                'Agustus'   => 'Agustus',
                                'September' => 'September',
                                'Oktober'   => 'Oktober',
                                'November'  => 'November',
                                'Desember'  => 'Desember',
                            ])
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Bulan periode wajib dipilih.',
                            ]),

                        TextInput::make('tahun')
                            ->label('Tahun')
                            ->placeholder('Contoh: 2026')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->default(now()->year),

                        TextInput::make('target_kas')
                            ->label('Target Kas')
                            ->placeholder('Contoh: 50000')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('status')
                            ->label('Status Periode')
                            ->options([
                                'aktif' => 'Aktif',
                                'tutup' => 'Tutup',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
