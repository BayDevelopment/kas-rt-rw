<?php

namespace App\Filament\Resources\Periodes\Pages;

use App\Filament\Resources\Periodes\PeriodeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriode extends CreateRecord
{
    protected static string $resource = PeriodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = \App\Models\Periode::query()
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'data.bulan' => 'Periode bulan dan tahun tersebut sudah terdaftar.',
                'data.tahun' => 'Periode bulan dan tahun tersebut sudah terdaftar.',
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
            ->body('Periode berhasil ditambahkan.')
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
