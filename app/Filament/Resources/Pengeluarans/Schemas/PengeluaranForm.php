<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Periode;
use App\Models\Tenants;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PengeluaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengeluaran Kas')
                    ->description('Catat seluruh pengeluaran kas RT/RW berdasarkan periode aktif.')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->schema([

                        Select::make('tenant_id')
                            ->label('Wilayah RT/RW')
                            ->options(fn () => Tenants::query()
                                ->where('is_active', true)
                                ->orderBy('nama')
                                ->pluck('nama', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->visible(fn () => Auth::user()?->isAdmin())
                            ->live(),

                        Select::make('periode_id')
                            ->label('Periode')
                            ->placeholder('Pilih periode aktif')
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
                                    ->get()
                                    ->mapWithKeys(fn ($periode) => [
                                        $periode->id => "{$periode->bulan} {$periode->tahun}",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->native(false)
                            ->required()
                            ->live(),

                        Select::make('kategori')
                            ->label('Kategori Pengeluaran')
                            ->options([
                                'operasional' => 'Operasional',
                                'sosial'      => 'Sosial',
                                'pembangunan' => 'Pembangunan',
                                'lainnya'     => 'Lainnya',
                            ])
                            ->native(false)
                            ->required(),

                        TextInput::make('jumlah')
                            ->label('Nominal Pengeluaran')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->minValue(1)
                            ->placeholder('Contoh: 250000')
                            ->helperText(function (callable $get) {
                                $tenantId = Auth::user()?->isAdmin()
                                    ? $get('tenant_id')
                                    : Auth::user()?->tenant_id;

                                $periodeId = $get('periode_id');

                                if (! $tenantId || ! $periodeId) {
                                    return new HtmlString(
                                        '<span class="text-gray-500">Pilih wilayah dan periode terlebih dahulu.</span>'
                                    );
                                }

                                $totalPemasukan = Pemasukan::query()
                                    ->where('tenant_id', $tenantId)
                                    ->where('periode_id', $periodeId)
                                    ->sum('jumlah');

                                $totalPengeluaran = Pengeluaran::query()
                                    ->where('tenant_id', $tenantId)
                                    ->where('periode_id', $periodeId)
                                    ->sum('jumlah');

                                $saldo = $totalPemasukan - $totalPengeluaran;

                                return new HtmlString(
                                    '<span class="text-danger-600 font-semibold">
                                        Saldo tersedia: Rp ' . number_format($saldo, 0, ',', '.') .
                                        '. Pengeluaran tidak boleh melebihi saldo.
                                    </span>'
                                );
                            })
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                    $tenantId = Auth::user()?->isAdmin()
                                        ? $get('tenant_id')
                                        : Auth::user()?->tenant_id;

                                    $periodeId = $get('periode_id');

                                    if (! $tenantId || ! $periodeId) {
                                        return;
                                    }

                                    $totalPemasukan = Pemasukan::query()
                                        ->where('tenant_id', $tenantId)
                                        ->where('periode_id', $periodeId)
                                        ->sum('jumlah');

                                    $totalPengeluaran = Pengeluaran::query()
                                        ->where('tenant_id', $tenantId)
                                        ->where('periode_id', $periodeId)
                                        ->sum('jumlah');

                                    $saldo = $totalPemasukan - $totalPengeluaran;

                                    if ((float) $value > (float) $saldo) {
                                        $fail('Saldo kas tidak mencukupi. Saldo tersedia Rp ' . number_format($saldo, 0, ',', '.') . '. Jika ada talangan ketua RT, masukkan dahulu sebagai pemasukan.');
                                    }
                                };
                            }),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Pengeluaran')
                            ->default(now())
                            ->native(false)
                            ->required(),

                        Textarea::make('keterangan')
                            ->label('Keterangan Pengeluaran')
                            ->placeholder('Contoh: Pembelian alat kebersihan lingkungan.')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
