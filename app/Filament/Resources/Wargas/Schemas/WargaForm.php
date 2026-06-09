<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ─── SEKSI: DATA PRIBADI ───────────────────────────────────
                Section::make('Data Pribadi')
                    ->description('Isi data sesuai dengan Kartu Tanda Penduduk (KTP) yang berlaku.')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([

                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Budi Santoso')
                            ->helperText('Tulis nama lengkap sesuai KTP, tanpa singkatan.')
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
                            ->regex('/^[\pL\s\'\-\.]+$/u') // hanya huruf, spasi, apostrof, tanda hubung, titik
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'max'      => 'Nama tidak boleh lebih dari 100 karakter.',
                                'min'      => 'Nama minimal 3 karakter.',
                                'regex'    => 'Nama hanya boleh mengandung huruf dan spasi.',
                            ])
                            ->extraInputAttributes(['autocomplete' => 'off']),

                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->placeholder('Contoh: 3201234567890001')
                            ->helperText('NIK terdiri dari 16 digit angka, tertera di KTP.')
                            ->required()
                            ->unique(table: 'wargas', column: 'nik', ignoreRecord: true)
                            ->length(16)
                            ->regex('/^[0-9]{16}$/')
                            ->validationMessages([
                                'required' => 'NIK wajib diisi.',
                                'unique'   => 'NIK ini sudah terdaftar dalam sistem.',
                                'length'   => 'NIK harus tepat 16 digit angka.',
                                'regex'    => 'NIK hanya boleh berisi 16 digit angka.',
                            ])
                            ->extraInputAttributes([
                                'autocomplete' => 'off',
                                'maxlength'    => '16',
                                'inputmode'    => 'numeric',
                            ]),

                        TextInput::make('no_hp')
                            ->label('Nomor HP / WhatsApp')
                            ->placeholder('Contoh: 08123456789')
                            ->helperText('Nomor yang bisa dihubungi, diawali 08 atau +62. Digunakan untuk keperluan informasi RT/RW.')
                            ->required()
                            ->tel()
                            ->regex('/^(\+62|62|0)8[1-9][0-9]{6,10}$/')
                            ->maxLength(15)
                            ->validationMessages([
                                'required' => 'Nomor HP wajib diisi.',
                                'regex'    => 'Format nomor HP tidak valid. Contoh: 08123456789.',
                                'max'      => 'Nomor HP tidak boleh lebih dari 15 karakter.',
                            ])
                            ->extraInputAttributes([
                                'autocomplete' => 'off',
                                'inputmode'    => 'tel',
                            ]),

                    ]),

                // ─── SEKSI: ALAMAT TEMPAT TINGGAL ─────────────────────────
                Section::make('Alamat Tempat Tinggal')
                    ->description('Isi alamat sesuai domisili di wilayah RT/RW ini.')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->schema([

                        TextInput::make('no_rumah')
                            ->label('Nomor Rumah')
                            ->placeholder('Contoh: 12A atau 05')
                            ->helperText('Nomor rumah sesuai yang tertera di pintu atau surat keterangan domisili.')
                            ->required()
                            ->maxLength(10)
                            ->regex('/^[a-zA-Z0-9\/\-]+$/')
                            ->validationMessages([
                                'required' => 'Nomor rumah wajib diisi.',
                                'max'      => 'Nomor rumah tidak boleh lebih dari 10 karakter.',
                                'regex'    => 'Nomor rumah hanya boleh berisi angka, huruf, garis miring, atau tanda hubung.',
                            ]),

                        Grid::make(2)
                            ->schema([

                                TextInput::make('rt')
                                    ->label('RT (Rukun Tetangga)')
                                    ->placeholder('Contoh: 001')
                                    ->helperText('Nomor RT tempat tinggal Anda, 3 digit.')
                                    ->required()
                                    ->maxLength(3)
                                    ->regex('/^\d{1,3}$/')
                                    ->validationMessages([
                                        'required' => 'Nomor RT wajib diisi.',
                                        'max'      => 'Nomor RT maksimal 3 digit.',
                                        'regex'    => 'Nomor RT hanya boleh berisi angka (maks. 3 digit).',
                                    ])
                                    ->extraInputAttributes(['inputmode' => 'numeric']),

                                TextInput::make('rw')
                                    ->label('RW (Rukun Warga)')
                                    ->placeholder('Contoh: 003')
                                    ->helperText('Nomor RW tempat tinggal Anda, 3 digit.')
                                    ->required()
                                    ->maxLength(3)
                                    ->regex('/^\d{1,3}$/')
                                    ->validationMessages([
                                        'required' => 'Nomor RW wajib diisi.',
                                        'max'      => 'Nomor RW maksimal 3 digit.',
                                        'regex'    => 'Nomor RW hanya boleh berisi angka (maks. 3 digit).',
                                    ])
                                    ->extraInputAttributes(['inputmode' => 'numeric']),

                            ]),

                    ]),

                // ─── SEKSI: STATUS KEANGGOTAAN ─────────────────────────────
                Section::make('Status Keanggotaan')
                    ->description('Status menentukan apakah warga aktif dalam sistem administrasi RT/RW.')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([

                        Select::make('status')
                            ->label('Status Warga')
                            ->helperText(
                                'Aktif: warga tinggal & tercatat di wilayah ini. ' .
                                    'Nonaktif: warga sudah pindah atau tidak lagi tercatat.'
                            )
                            ->options([
                                'aktif'    => '✅  Aktif',
                                'nonaktif' => '🚫  Nonaktif',
                            ])
                            ->default('aktif')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Status warga wajib dipilih.',
                            ]),

                        Select::make('jabatan')
                            ->label('Jabatan Warga')
                            ->helperText('Pilih jabatan warga dalam struktur RT/RW.')
                            ->options([
                                'warga'         => 'Warga',
                                'ketua_rt'      => 'Ketua RT',
                                'sekretaris_rt' => 'Sekretaris RT',
                                'bendahara_rt'  => 'Bendahara RT',
                                'ketua_rw'      => 'Ketua RW',
                                'sekretaris_rw' => 'Sekretaris RW',
                                'bendahara_rw'  => 'Bendahara RW',
                            ])
                            ->default('warga')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Jabatan warga wajib dipilih.',
                            ]),
                    ]),

            ]);
    }
}
