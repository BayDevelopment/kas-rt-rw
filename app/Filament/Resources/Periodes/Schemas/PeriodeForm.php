<?php

namespace App\Filament\Resources\Periodes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->helperText('Isi tahun periode iuran, misalnya 2026.')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->length(4)
                            ->validationMessages([
                                'required' => 'Tahun periode wajib diisi.',
                                'numeric'  => 'Tahun harus berupa angka.',
                                'min'      => 'Tahun minimal 2020.',
                                'max'      => 'Tahun maksimal 2100.',
                                'length'   => 'Tahun harus terdiri dari 4 digit.',
                            ])
                            ->extraInputAttributes([
                                'inputmode' => 'numeric',
                                'maxlength' => '4',
                            ]),

                        TextInput::make('target_kas')
                            ->label('Target Kas')
                            ->placeholder('Contoh: 50000')
                            ->helperText('Nominal target kas untuk periode ini.')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0)
                            ->validationMessages([
                                'required' => 'Target kas wajib diisi.',
                                'numeric'  => 'Target kas harus berupa angka.',
                                'min'      => 'Target kas tidak boleh kurang dari 0.',
                            ]),

                        Select::make('status')
                            ->label('Status Periode')
                            ->placeholder('Pilih status periode')
                            ->helperText('Aktif: masih berjalan. Tutup: periode sudah selesai.')
                            ->options([
                                'aktif' => 'Aktif',
                                'tutup' => 'Tutup',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Status periode wajib dipilih.',
                            ]),
                    ]),
            ]);
    }
}
