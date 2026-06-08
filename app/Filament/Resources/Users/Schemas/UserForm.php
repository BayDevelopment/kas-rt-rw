<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Akun')
                    ->description('Masukkan identitas pengguna dan data login dengan benar.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Ahmad Fauzi')
                            ->required()
                            ->minLength(3)
                            ->maxLength(100)
                            ->autocomplete('name')
                            ->helperText('Masukkan nama lengkap pengguna minimal 3 karakter.')
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'min'      => 'Nama lengkap minimal terdiri dari 3 karakter.',
                                'max'      => 'Nama lengkap tidak boleh lebih dari 100 karakter.',
                            ]),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('Contoh: pengguna@email.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->autocomplete('email')
                            ->dehydrateStateUsing(
                                fn (string $state): string => strtolower(trim($state))
                            )
                            ->helperText('Gunakan alamat email aktif. Satu alamat email hanya dapat digunakan oleh satu akun.')
                            ->validationMessages([
                                'required' => 'Alamat email wajib diisi.',
                                'email'    => 'Format alamat email belum benar. Contoh: pengguna@email.com.',
                                'unique'   => 'Alamat email tersebut sudah digunakan oleh akun lain.',
                                'max'      => 'Alamat email terlalu panjang.',
                            ]),

                    ])
                    ->columns(2),

                Section::make('Keamanan Password')
                    ->description('Gunakan password yang tidak mudah ditebak dan jangan membagikannya kepada pihak lain.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->autocomplete('new-password')
                            ->required(
                                fn (string $operation): bool => $operation === 'create'
                            )
                            ->rules([
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers(),
                            ])
                            ->dehydrated(
                                fn (?string $state): bool => filled($state)
                            )
                            ->afterStateHydrated(
                                fn (TextInput $component) => $component->state(null)
                            )
                            ->helperText(
                                'Minimal 8 karakter, menggunakan huruf besar, huruf kecil, dan angka. '
                                . 'Saat mengedit akun, kosongkan kolom ini apabila password tidak ingin diubah.'
                            )
                            ->validationMessages([
                                'required' => 'Password wajib diisi untuk membuat akun baru.',
                            ]),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->autocomplete('new-password')
                            ->required(
                                fn (string $operation): bool => $operation === 'create'
                            )
                            ->rules([
                                'required_with:password',
                                'same:password',
                            ])
                            ->dehydrated(false)
                            ->afterStateHydrated(
                                fn (TextInput $component) => $component->state(null)
                            )
                            ->helperText('Ketik ulang password untuk memastikan tidak ada kesalahan penulisan.')
                            ->validationMessages([
                                'required'      => 'Konfirmasi password wajib diisi.',
                                'required_with' => 'Silakan ketik ulang password baru.',
                                'same'          => 'Konfirmasi password belum sama dengan password yang dimasukkan.',
                            ]),

                    ])
                    ->columns(2),

                Section::make('Hak Akses')
                    ->description('Tentukan peran dan status akun dengan hati-hati.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([

                        Select::make('role')
                            ->label('Peran Pengguna')
                            ->options([
                                'admin'       => 'Admin',
                                'pengurus_rw' => 'Pengurus RW',
                                'bendahara_rt'=> 'Bendahara RT',
                                'warga'       => 'Warga',
                            ])
                            ->default('warga')
                            ->required()
                            ->native(false)
                            ->helperText('Pilih hak akses sesuai tanggung jawab pengguna. Jangan memberikan akses admin kepada pengguna biasa.')
                            ->validationMessages([
                                'required' => 'Peran pengguna wajib dipilih.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan akun apabila pengguna tidak diperbolehkan login untuk sementara waktu.'),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Wilayah dan Kontak
                |--------------------------------------------------------------------------
                */
                Section::make('Data Wilayah dan Kontak')
                    ->description('Lengkapi data wilayah serta nomor telepon pengguna apabila tersedia.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([

                        TextInput::make('rt')
                            ->label('RT')
                            ->placeholder('Contoh: 001')
                            ->inputMode('numeric')
                            ->minLength(3)
                            ->maxLength(3)
                            ->regex('/^[0-9]{3}$/')
                            ->helperText('Gunakan tepat 3 digit angka. Contoh: 001.')
                            ->validationMessages([
                                'min'   => 'Nomor RT harus terdiri dari 3 digit.',
                                'max'   => 'Nomor RT harus terdiri dari 3 digit.',
                                'regex' => 'Nomor RT hanya boleh berisi 3 digit angka. Contoh: 001.',
                            ]),

                        TextInput::make('rw')
                            ->label('RW')
                            ->placeholder('Contoh: 005')
                            ->inputMode('numeric')
                            ->minLength(3)
                            ->maxLength(3)
                            ->regex('/^[0-9]{3}$/')
                            ->helperText('Gunakan tepat 3 digit angka. Contoh: 005.')
                            ->validationMessages([
                                'min'   => 'Nomor RW harus terdiri dari 3 digit.',
                                'max'   => 'Nomor RW harus terdiri dari 3 digit.',
                                'regex' => 'Nomor RW hanya boleh berisi 3 digit angka. Contoh: 005.',
                            ]),

                        TextInput::make('no_hp')
                            ->label('Nomor Telepon')
                            ->placeholder('Contoh: 081234567890')
                            ->tel()
                            ->minLength(10)
                            ->maxLength(15)
                            ->regex('/^\+?[0-9]+$/')
                            ->helperText('Gunakan 10 sampai 15 digit angka. Awalan + diperbolehkan, misalnya +6281234567890.')
                            ->validationMessages([
                                'min'   => 'Nomor telepon minimal terdiri dari 10 digit.',
                                'max'   => 'Nomor telepon maksimal terdiri dari 15 karakter.',
                                'regex' => 'Nomor telepon hanya boleh berisi angka dan dapat diawali dengan tanda +.',
                            ]),

                        Select::make('warga_id')
                            ->label('Hubungkan dengan Data Warga')
                            ->relationship(
                                name: 'warga',
                                titleAttribute: 'nama',
                            )
                            ->searchable()
                            ->preload()
                            ->unique(ignoreRecord: true)
                            ->helperText('Opsional. Pilih data warga apabila akun ini dimiliki oleh warga yang sudah terdaftar.')
                            ->validationMessages([
                                'unique' => 'Data warga tersebut sudah terhubung dengan akun lain.',
                            ]),

                    ])
                    ->columns(2),

            ]);
    }
}
