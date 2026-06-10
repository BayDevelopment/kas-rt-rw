<?php

namespace App\Filament\Resources\Pemasukans\Schemas;

use App\Models\Periode;
use App\Models\Tenants;
use App\Models\Warga;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PemasukanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pemasukan Kas')
                    ->description('Catat pemasukan kas warga berdasarkan periode iuran.')
                    ->icon('heroicon-o-banknotes')
                    ->columnSpanFull()
                    ->schema([

                        Select::make('tenant_id')
                            ->label('Wilayah RT/RW')
                            ->options(
                                fn() => Tenants::query()
                                    ->where('is_active', true)
                                    ->orderBy('nama')
                                    ->pluck('nama', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(false)
                            ->native(false)
                            ->required()
                            ->visible(fn() => Auth::user()?->isAdmin())
                            ->live()
                            ->validationMessages([
                                'required' => 'Wilayah RT/RW wajib dipilih.',
                            ]),

                        Select::make('warga_id')
                            ->label('Warga')
                            ->placeholder('Pilih warga')
                            ->options(function (callable $get) {
                                $tenantId = Auth::user()?->isAdmin()
                                    ? $get('tenant_id')
                                    : Auth::user()?->tenant_id;

                                if (! $tenantId) {
                                    return [];
                                }

                                return Warga::query()
                                    ->where('tenant_id', $tenantId)
                                    ->where('status', 'aktif')
                                    ->orderBy('nama')
                                    ->pluck('nama', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload(false)
                            ->native(false)
                            ->required()
                            ->validationMessages([
                                'required' => 'Warga wajib dipilih.',
                            ]),

                        Select::make('periode_id')
                            ->label('Periode Iuran')
                            ->placeholder('Pilih periode')
                            ->options(function (callable $get) {
                                $tenantId = Auth::user()?->isAdmin()
                                    ? $get('tenant_id')
                                    : Auth::user()?->tenant_id;

                                if (! $tenantId) {
                                    return [];
                                }

                                return Periode::query()
                                    ->where('tenant_id', $tenantId)
                                    ->where('status', 'aktif')
                                    ->orderByDesc('tahun')
                                    ->orderBy('bulan')
                                    ->get()
                                    ->mapWithKeys(fn($periode) => [
                                        $periode->id => $periode->bulan . ' ' . $periode->tahun,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload(false)
                            ->native(false)
                            ->required()
                            ->validationMessages([
                                'required' => 'Periode iuran wajib dipilih.',
                            ]),

                        TextInput::make('jumlah')
                            ->label('Jumlah Pemasukan')
                            ->placeholder('Contoh: 50000')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->validationMessages([
                                'required' => 'Jumlah pemasukan wajib diisi.',
                                'numeric' => 'Jumlah pemasukan harus berupa angka.',
                                'min' => 'Jumlah pemasukan minimal Rp1.',
                            ]),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Pemasukan')
                            ->placeholder('Pilih tanggal pemasukan')
                            ->default(now())
                            ->native(false)
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal pemasukan wajib diisi.',
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: Iuran kas warga bulan Juni')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }
}
