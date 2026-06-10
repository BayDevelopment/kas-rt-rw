<?php

namespace App\Filament\Resources\Pengeluarans\Pages;

use App\Filament\Resources\Pengeluarans\PengeluaranResource;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreatePengeluaran extends CreateRecord
{
    protected static string $resource = PengeluaranResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['tenant_id'])) {
            $data['tenant_id'] = Auth::user()->tenant_id;
        }

        $totalPemasukan = Pemasukan::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('periode_id', $data['periode_id'])
            ->sum('jumlah');

        $totalPengeluaran = Pengeluaran::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('periode_id', $data['periode_id'])
            ->sum('jumlah');

        $saldo = $totalPemasukan - $totalPengeluaran;

        if ((float) $data['jumlah'] > (float) $saldo) {
            throw ValidationException::withMessages([
                'jumlah' => 'Saldo kas tidak mencukupi. Saldo tersedia Rp ' . number_format($saldo, 0, ',', '.') . '. Jika ada talangan ketua RT, masukkan dahulu sebagai pemasukan.',
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Pengeluaran berhasil ditambahkan.')
            ->success();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Create')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Create & Create Another')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-x-mark')
                ->color('gray'),
        ];
    }
}
