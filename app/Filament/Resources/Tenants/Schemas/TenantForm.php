<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Wilayah RT/RW')
                    ->description('Data ini digunakan sebagai identitas wilayah atau pelanggan aplikasi E-KAS.')
                    ->icon('heroicon-o-building-office-2')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Wilayah')
                            ->placeholder('Contoh: RW 003 Kelurahan Kotabumi')
                            ->helperText('Isi nama wilayah RT/RW, perumahan, atau lingkungan yang menggunakan aplikasi.')
                            ->required()
                            ->minLength(3)
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (filled($state)) {
                                    $set('kode', Str::slug($state));
                                }
                            })
                            ->validationMessages([
                                'required' => 'Nama wilayah wajib diisi.',
                                'min' => 'Nama wilayah minimal 3 karakter.',
                                'max' => 'Nama wilayah maksimal 100 karakter.',
                            ]),

                        TextInput::make('kode')
                            ->label('Kode Wilayah')
                            ->placeholder('Contoh: rw-003-kotabumi')
                            ->helperText('Kode unik untuk membedakan setiap wilayah. Gunakan huruf kecil, angka, dan tanda hubung.')
                            ->required()
                            ->unique(table: 'tenants', column: 'kode', ignoreRecord: true)
                            ->minLength(3)
                            ->maxLength(100)
                            ->regex('/^[a-z0-9\-]+$/')
                            ->validationMessages([
                                'required' => 'Kode wilayah wajib diisi.',
                                'unique' => 'Kode wilayah sudah digunakan.',
                                'min' => 'Kode wilayah minimal 3 karakter.',
                                'max' => 'Kode wilayah maksimal 100 karakter.',
                                'regex' => 'Kode hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
                            ]),

                        TextInput::make('alamat')
                            ->label('Alamat')
                            ->placeholder('Contoh: Jl. Raya Kotabumi No. 10')
                            ->helperText('Alamat utama wilayah atau sekretariat RT/RW.')
                            ->maxLength(150)
                            ->columnSpanFull(),

                        TextInput::make('kelurahan')
                            ->label('Kelurahan')
                            ->placeholder('Contoh: Kotabumi')
                            ->maxLength(100),

                        TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->placeholder('Contoh: Purwakarta')
                            ->maxLength(100),

                        TextInput::make('kota')
                            ->label('Kota/Kabupaten')
                            ->placeholder('Contoh: Cilegon')
                            ->maxLength(100),

                        TextInput::make('provinsi')
                            ->label('Provinsi')
                            ->placeholder('Contoh: Banten')
                            ->maxLength(100),

                        Select::make('is_active')
                            ->label('Status Wilayah')
                            ->helperText('Aktif: wilayah dapat menggunakan sistem. Nonaktif: akses wilayah dapat dibatasi.')
                            ->options([
                                true => 'Aktif',
                                false => 'Nonaktif',
                            ])
                            ->default(true)
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
