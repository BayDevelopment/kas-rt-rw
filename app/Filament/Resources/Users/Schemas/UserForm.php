<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Tenants;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
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
                                fn(string $state): string => strtolower(trim($state))
                            )
                            ->helperText('Gunakan alamat email aktif. Satu alamat email hanya dapat digunakan oleh satu akun.')
                            ->validationMessages([
                                'required' => 'Alamat email wajib diisi.',
                                'email'    => 'Format alamat email belum benar. Contoh: pengguna@email.com.',
                                'unique'   => 'Alamat email tersebut sudah digunakan oleh akun lain.',
                                'max'      => 'Alamat email terlalu panjang.',
                            ]),

                    ])
                    ->columnSpanFull(),

                Section::make('Keamanan Password')
                    ->description('Gunakan password yang tidak mudah ditebak dan jangan membagikannya kepada pihak lain.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->autocomplete('new-password')
                            ->required(
                                fn(string $operation): bool => $operation === 'create'
                            )
                            ->rules([
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers(),
                            ])
                            ->dehydrated(
                                fn(?string $state): bool => filled($state)
                            )
                            ->afterStateHydrated(
                                fn(TextInput $component) => $component->state(null)
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
                                fn(string $operation): bool => $operation === 'create'
                            )
                            ->rules([
                                'required_with:password',
                                'same:password',
                            ])
                            ->dehydrated(false)
                            ->afterStateHydrated(
                                fn(TextInput $component) => $component->state(null)
                            )
                            ->helperText('Ketik ulang password untuk memastikan tidak ada kesalahan penulisan.')
                            ->validationMessages([
                                'required'      => 'Konfirmasi password wajib diisi.',
                                'required_with' => 'Silakan ketik ulang password baru.',
                                'same'          => 'Konfirmasi password belum sama dengan password yang dimasukkan.',
                            ]),

                    ])
                    ->columnSpanFull(),

                Section::make('Hak Akses')
                    ->description('Tentukan tenant, peran, dan status akun.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([

                        Select::make('tenant_id')
                            ->label('Tenant / Wilayah')
                            ->options(
                                fn(): array => Tenants::query()
                                    ->orderBy('nama')
                                    ->pluck('nama', 'id')
                                    ->toArray()
                            )
                            ->default(fn(): ?int => Auth::user()?->tenant_id)
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->disabled(fn(): bool => ! Auth::user()?->isAdmin())
                            ->dehydrated(true)
                            ->helperText(
                                fn(): string => Auth::user()?->isAdmin()
                                    ? 'Admin dapat memilih tenant untuk akun ini.'
                                    : 'Tenant otomatis mengikuti wilayah akun Anda.'
                            ),

                        Select::make('role')
                            ->label('Peran Pengguna')
                            ->options([
                                'admin'        => 'Admin',
                                'pengurus_rw'  => 'Pengurus RW',
                                'pengurus_rt'  => 'Pengurus RT',
                                'bendahara_rw' => 'Bendahara RW',
                                'bendahara_rt' => 'Bendahara RT',
                                'warga'        => 'Warga',
                            ])
                            ->default('warga')
                            ->required()
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Akun Aktif')
                            ->default(true),

                    ])->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Wilayah dan Kontak
                |--------------------------------------------------------------------------
                */
                Section::make('Data Wilayah dan Kontak')
                    ->description('Lengkapi data wilayah serta nomor telepon pengguna apabila tersedia.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([

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
                    ->columnSpanFull(),

            ]);
    }
}
